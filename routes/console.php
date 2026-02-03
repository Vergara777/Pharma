<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Verificar stock bajo cada hora
Schedule::command('stock:check')->hourly();

// Resetear ventas diarias a medianoche (hora de Colombia)
Schedule::command('sales:reset-daily')
    ->dailyAt('00:00')
    ->timezone('America/Bogota');
