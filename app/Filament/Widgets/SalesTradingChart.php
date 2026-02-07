<?php

namespace App\Filament\Widgets;

use App\Models\Ventas;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalesTradingChart extends ChartWidget
{
    protected static ?int $sort = 10;
    protected ?string $heading = '📊 Trading Terminal: Análisis MACD de Ventas';
    protected int | string | array $columnSpan = 'full';
    
    protected function getData(): array
    {
        // Necesitamos más días para que las medias móviles (EMA) se estabilicen
        $lookbackDays = 60;
        $displayDays = 30;
        
        $startDate = now()->subDays($lookbackDays)->startOfDay();
        $endDate = now()->endOfDay();

        $ventas = Ventas::where('status', 'active')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $mappedData = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateString = $current->toDateString();
            $venta = $ventas->firstWhere('date', $dateString);
            $mappedData[$dateString] = $venta ? (int) $venta->total_count : 0;
            $current->addDay();
        }

        $allValues = array_values($mappedData);
        $allLabels = array_keys($mappedData);

        // --- Cálculos de Trading ---
        
        // 1. EMA (Exponential Moving Average) Helper
        $calculateEma = function($data, $period) {
            $ema = [];
            $k = 2 / ($period + 1);
            
            // Valor inicial (SMA simple para el primer punto)
            $ema[0] = $data[0];
            
            for ($i = 1; $i < count($data); $i++) {
                $ema[$i] = ($data[$i] * $k) + ($ema[$i - 1] * (1 - $k));
            }
            return $ema;
        };

        // 2. MACD: EMA(12) - EMA(26)
        $ema12 = $calculateEma($allValues, 12);
        $ema26 = $calculateEma($allValues, 26);
        $macdLine = [];
        for ($i = 0; $i < count($allValues); $i++) {
            $macdLine[$i] = $ema12[$i] - $ema26[$i];
        }

        // 3. Signal Line: EMA(9) del MACD
        $signalLine = $calculateEma($macdLine, 9);

        // 4. Histogram: MACD - Signal
        $histogram = [];
        $histogramColors = [];
        for ($i = 0; $i < count($allValues); $i++) {
            $val = $macdLine[$i] - $signalLine[$i];
            $histogram[$i] = round($val, 2);
            // Color según si es positivo (bullish) o negativo (bearish)
            $histogramColors[$i] = $val >= 0 ? 'rgba(34, 197, 94, 0.5)' : 'rgba(239, 68, 68, 0.5)';
        }

        // Recortamos para mostrar solo los últimos displayDays
        $startIndex = $lookbackDays - $displayDays;
        $finalValues = array_slice($allValues, $startIndex);
        $finalLabels = array_map(fn($d) => Carbon::parse($d)->format('d M'), array_slice($allLabels, $startIndex));
        $finalMacd = array_slice($macdLine, $startIndex);
        $finalSignal = array_slice($signalLine, $startIndex);
        $finalHistogram = array_slice($histogram, $startIndex);
        $finalColors = array_slice($histogramColors, $startIndex);

        return [
            'datasets' => [
                [
                    'label' => 'Impulso (Histograma)',
                    'data' => $finalHistogram,
                    'type' => 'bar',
                    'backgroundColor' => $finalColors,
                    'borderColor' => 'transparent',
                    'borderWidth' => 1,
                    'order' => 3,
                ],
                [
                    'label' => 'Ventas (Volumen)',
                    'data' => $finalValues,
                    'type' => 'line',
                    'borderColor' => 'rgba(150, 150, 150, 0.3)',
                    'backgroundColor' => 'transparent',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'pointRadius' => 0,
                    'order' => 4,
                ],
                [
                    'label' => 'Línea MACD',
                    'data' => $finalMacd,
                    'type' => 'line',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'pointRadius' => 0,
                    'tension' => 0.3,
                    'order' => 1,
                ],
                [
                    'label' => 'Señal (Trend)',
                    'data' => $finalSignal,
                    'type' => 'line',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 2,
                    'pointRadius' => 0,
                    'tension' => 0.3,
                    'order' => 2,
                ],
            ],
            'labels' => $finalLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Usamos line como base pero mezclamos datasets
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => ['usePointStyle' => true],
                ],
            ],
            'scales' => [
                'y' => [
                    'grid' => ['color' => 'rgba(200, 200, 200, 0.05)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
        ];
    }
}
