<?php
 
namespace App\Services\Factus;

// 1. IMPORTANTE:Importar las clases necesarias
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FactusService
{
   private $url;
   private $grantType;
   private $clientId;
   private $clientSecret;
   private $username;
   private $password;
   
   public function __construct()
   {
      $this->url = config('services.Factus.url');
      $this->grantType = config('services.Factus.grant_type');
      $this->clientId = config('services.Factus.client_id');
      $this->clientSecret = config('services.Factus.client_secret');
      $this->username = config('services.Factus.username');
      $this->password = config('services.Factus.password');
   }

   
    public function getToken()
    {
        // 1. Intentar obtener el token del Cache
        $token = Cache::get('factus_token');

        if ($token) {
            return $token;
        }

        // 2. Si no hay token, pedir uno nuevo
        $response = Http::asForm()->post($this->url . '/oauth/token', [
            'grant_type' => $this->grantType,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            // Guardar en cache por casi 1 hora (ej: 50 min) para mayor seguridad
            Cache::put('factus_token', $data, 3000);
            return $data;
        }

        return null;
    }
}
