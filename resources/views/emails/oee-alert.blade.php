<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OEE Alert</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Roboto', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f0f2f5;">
    <div style="max-width: 650px; margin: 20px auto; background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #ff4757 0%, #ff6b81 100%); padding: 25px; text-align: center;">
            <h1 style="color: #fff; margin: 0; font-size: 24px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">⚠️ OEE Alert Notification</h1>
        </div>
        
        <!-- Content -->
        <div style="padding: 30px;">
            <div style="background-color: #fff5f5; border-left: 5px solid #ff4757; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                <p style="margin-top: 0; font-size: 16px; font-weight: 500;">OEE Score untuk mesin <strong style="color: #ff4757;">{{ $machine->name }}</strong> berada di bawah target yang ditentukan.</p>
            </div>
            
            <!-- OEE Details Card -->
            <div style="background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 20px; margin-bottom: 25px; border-top: 4px solid #ff4757;">
                <h2 style="margin-top: 0; color: #333; font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 10px;">📊 Detail OEE</h2>
                
                <div style="display: flex; justify-content: space-between; margin: 20px 0; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 120px; text-align: center; padding: 15px; background-color: #fff5f5; border-radius: 6px; margin: 5px;">
                        <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Current OEE</div>
                        <div style="font-size: 24px; font-weight: 700; color: #ff4757;">{{ number_format($oeeScore, 2) }}%</div>
                    </div>
                    
                    <div style="flex: 1; min-width: 120px; text-align: center; padding: 15px; background-color: #f0fff4; border-radius: 6px; margin: 5px;">
                        <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Target OEE</div>
                        <div style="font-size: 24px; font-weight: 700; color: #28a745;">{{ number_format($targetOee, 2) }}%</div>
                    </div>
                    
                    <div style="flex: 1; min-width: 120px; text-align: center; padding: 15px; background-color: #fff5f5; border-radius: 6px; margin: 5px;">
                        <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Selisih</div>
                        <div style="font-size: 24px; font-weight: 700; color: #ff4757;">-{{ number_format($difference, 2) }}%</div>
                    </div>
                </div>
                
                <!-- OEE Visualization (replacing progress bar) -->
                <div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 6px;">
                    <div style="text-align: center; margin-bottom: 10px;">
                        <span style="font-size: 14px; color: #666;">OEE Performance</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: center;">
                        <div style="font-size: 36px; font-weight: 700; color: #ff4757; text-align: center;">
                            {{ number_format($oeeScore, 1) }}%
                        </div>
                        <div style="margin-left: 15px; padding-left: 15px; border-left: 1px solid #ddd;">
                            <div style="font-size: 14px; color: #666;">Target</div>
                            <div style="font-size: 20px; font-weight: 500; color: #28a745;">{{ number_format($targetOee, 1) }}%</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Production Details Card -->
            @if(isset($production) && $production)
            <div style="background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 20px; margin-bottom: 25px; border-top: 4px solid #007bff;">
                <h2 style="margin-top: 0; color: #333; font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 10px;">🏭 Detail Produksi</h2>
                
                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    @if(isset($production->product) && $production->product && isset($production->product->name))
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; color: #666; width: 40%;">Produk</td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: 500;">{{ $production->product->name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; color: #666;">Batch</td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: 500;">{{ $production->batch_number ?? 'N/A' }}</td>
                    </tr>
                    @if(isset($production->start_time))
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; color: #666;">Tanggal</td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: 500;">{{ $production->start_time->format('d M Y') }}</td>
                    </tr>
                    @endif
                    @if(isset($production->shift) && $production->shift)
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; color: #666;">Shift</td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: 500;">{{ $production->shift->name }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            @endif
            
            <!-- Action Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $url ?? route('manajerial.dashboard') }}" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: #fff; text-decoration: none; border-radius: 50px; font-weight: 500; text-align: center; box-shadow: 0 4px 10px rgba(0,123,255,0.3); transition: all 0.3s;">Lihat Detail Mesin</a>
            </div>
            
            <p style="margin: 25px 0; padding: 15px; background-color: #f8f9fa; border-radius: 4px; font-size: 15px; color: #555; line-height: 1.6;">
                Mohon segera lakukan pengecekan dan tindakan yang diperlukan untuk meningkatkan efisiensi produksi. Performa mesin yang optimal akan membantu mencapai target produksi yang telah ditetapkan.
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #eee;">
            <p style="margin: 0; color: #666; font-size: 14px;">
                Terima kasih,<br>
                <strong>{{ config('app.name') }}</strong>
            </p>
            <p style="margin-top: 15px; color: #999; font-size: 12px;">
                Email ini dikirim secara otomatis. Mohon jangan membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>

@component('mail::message')
# ALERT: OEE Di Bawah Target

<div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
    <h2 style="margin-top: 0;">Perhatian!</h2>
    <p>OEE untuk mesin <strong>{{ $machineName }}</strong> berada di bawah target.</p>
</div>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <tr>
        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Mesin</td>
        <td style="padding: 10px; border: 1px solid #ddd;">{{ $machineName }}</td>
    </tr>
    <tr>
        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">OEE Score</td>
        <td style="padding: 10px; border: 1px solid #ddd;">{{ number_format($oeeScore, 2) }}%</td>
    </tr>
    <tr>
        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Target OEE</td>
        <td style="padding: 10px; border: 1px solid #ddd;">{{ number_format($targetOee, 2) }}%</td>
    </tr>
    <tr>
        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Selisih</td>
        <td style="padding: 10px; border: 1px solid #ddd;">{{ number_format($targetOee - $oeeScore, 2) }}%</td>
    </tr>
    @if(isset($production) && $production)
    <tr>
        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Production ID</td>
        <td style="padding: 10px; border: 1px solid #ddd;">{{ $production->id }}</td>
    </tr>
    <tr>
        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Status</td>
        <td style="padding: 10px; border: 1px solid #ddd;">{{ ucfirst($production->status) }}</td>
    </tr>
    @endif
</table>

<p>Silakan periksa dashboard OEE untuk detail lebih lanjut.</p>


Terima kasih,<br>
{{ config('app.name') }}
@endcomponent