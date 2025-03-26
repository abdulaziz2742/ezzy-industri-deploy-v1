<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Production;

class WhatsAppService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.whatsapp.api_key');
        $this->apiUrl = config('services.whatsapp.api_url', 'https://api.fonnte.com/send');
        
        Log::info('WhatsApp config', [
            'api_url' => $this->apiUrl,
            'api_key_exists' => !empty($this->apiKey)
        ]);
    }

    public function sendOeeAlert($phone, $machine, $oeeScore, $targetOee, $production = null)
    {
        try {
            $machineName = is_object($machine) ? $machine->name : (is_string($machine) ? $machine : 'Unknown Machine');
            
            Log::info('Preparing WhatsApp message', [
                'phone' => $phone,
                'machine' => $machineName,
                'oee_score' => $oeeScore
            ]);

            $message = "⚠️ *OEE Alert*\n\n"
                    . "Machine: *{$machineName}*\n"
                    . "Current OEE: *{$oeeScore}%*\n"
                    . "Target OEE: *{$targetOee}%*";

            if ($production) {
                $message .= "\nProduction ID: *{$production->id}*\n"
                        . "Status: *{$production->status}*";
            }

            Log::info('Sending WhatsApp message', [
                'target' => $phone,
                'message' => $message
            ]);

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])
            ->withoutVerifying() // Disable SSL verification for development
            ->post($this->apiUrl, [
                'target' => $phone,
                'message' => $message,
            ]);

            Log::info('WhatsApp API Response', [
                'status_code' => $response->status(),
                'response' => $response->json()
            ]);

            if (!$response->successful()) {
                throw new \Exception('WhatsApp API error: ' . $response->body());
            }

            return true;

        } catch (\Exception $e) {
            Log::error('WhatsApp Alert Failed', [
                'error' => $e->getMessage(),
                'phone' => $phone,
                'machine' => $machineName
            ]);
            return false;
        }
    }
}