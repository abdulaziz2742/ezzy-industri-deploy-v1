<?php

namespace App\Traits;

use App\Notifications\OeeAlertNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\OeeAlert;
use App\Models\Production;
use App\Models\OeeRecord;
use App\Services\WhatsAppService;

trait OeeAlertTrait
{
    protected function checkAndSendOeeAlert($machine, $oeeScore, $productionId = null)
    {
        try {
            // Pastikan machine tidak null
            if (!$machine) {
                Log::error("OEE Alert skipped: machine is null");
                return false;
            }

            // Log untuk debugging dengan null checks
            Log::info("Checking OEE alert conditions", [
                'machine' => $machine->name ?? 'Unknown',
                'oee_score' => $oeeScore,
                'target' => $machine->oee_target ?? 'Unknown',
                'alert_enabled' => $machine->alert_enabled ?? false,
                'production_id' => $productionId,
                'alert_email' => $machine->alert_email ?? null,
                'alert_phone' => $machine->alert_phone ?? null,
                'is_test' => $productionId === null
            ]);

            // Jika alert tidak diaktifkan, keluar
            if (!($machine->alert_enabled ?? false)) {
                Log::info("OEE Alert skipped: alerts not enabled for machine " . ($machine->name ?? 'Unknown'));
                return false;
            }

            // Periksa apakah email atau nomor telepon dikonfigurasi
            if (empty($machine->alert_email) && empty($machine->alert_phone)) {
                Log::warning("OEE Alert skipped: no email or phone configured for machine " . ($machine->name ?? 'Unknown'));
                return false;
            }

            // Cek production jika ada ID
            if ($productionId) {
                $production = Production::find($productionId);
                
                Log::info("Production status check", [
                    'production_id' => $productionId,
                    'production_found' => $production ? 'yes' : 'no',
                    'end_time' => $production ? $production->end_time : null,
                    'status' => $production ? $production->status : null
                ]);

                if (!$production) {
                    Log::info("OEE Alert skipped: production not found");
                    return false;
                }

                // Cek apakah alert sudah pernah dikirim
                $existingAlert = OeeAlert::where('production_id', $productionId)
                    ->where('machine_id', $machine->id)
                    ->exists();
                
                if ($existingAlert) {
                    Log::info("OEE Alert skipped: alert already sent for this production");
                    return false;
                }
            }

            // Cek OEE di bawah target
            $targetOee = $machine->oee_target ?? 85.00; // Default target if not set
            if ($oeeScore < $targetOee) {
                Log::info("OEE is below target, preparing to send alerts", [
                    'oee_score' => $oeeScore,
                    'oee_target' => $targetOee
                ]);

                // Buat record alert
                if ($productionId) {
                    OeeAlert::create([
                        'production_id' => $productionId,
                        'machine_id' => $machine->id,
                        'oee_score' => $oeeScore,
                        'target_oee' => $targetOee,
                        'sent_at' => now(),
                    ]);
                    Log::info("OEE Alert record created in database");
                }

                // Kirim email jika ada
                if (!empty($machine->alert_email)) {
                    try {
                        Notification::route('mail', $machine->alert_email)
                            ->notify(new OeeAlertNotification($machine, $oeeScore, $targetOee, $productionId));
                        Log::info("OEE Alert email sent for machine " . ($machine->name ?? 'Unknown') . " with score {$oeeScore}%");
                    } catch (\Exception $emailError) {
                        Log::error("Email error: " . $emailError->getMessage());
                    }
                }

                // Kirim WhatsApp jika ada
                if (!empty($machine->alert_phone)) {
                    try {
                        $whatsappService = app(WhatsAppService::class);
                        $result = $whatsappService->sendOeeAlert(
                            $machine->alert_phone,
                            $machine,
                            $oeeScore,
                            $targetOee,
                            $production ?? null
                        );
                        
                        if ($result) {
                            Log::info("OEE Alert WhatsApp sent for machine " . ($machine->name ?? 'Unknown') . " with score {$oeeScore}%");
                        } else {
                            Log::error("Failed to send WhatsApp alert for machine " . ($machine->name ?? 'Unknown'));
                        }
                    } catch (\Exception $whatsappError) {
                        Log::error("WhatsApp error: " . $whatsappError->getMessage());
                    }
                }

                return true;
            }

            Log::info("OEE Alert skipped: OEE score {$oeeScore} is not below target {$targetOee}");
            return false;
        } catch (\Exception $e) {
            Log::error("Error in checkAndSendOeeAlert: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }
}