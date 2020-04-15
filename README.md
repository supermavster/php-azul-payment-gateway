# PHP Azul Payment Gateway - Supermavster
![img](https://www.azul.com.do/SiteAssets/v2Theme/images/header/Azul_home_logo.png) | *Azul Payment Gateway for accepting payments on your Website (E-Commerce) with PHP by Supermavster.*
--|--



Encontrar un buen referente para utilizar Azul como pasarela de pagos mediante PHP es difícil y al poder encontrar un proyecto es enredado y un poco tortuoso; es por ello por lo que, realice este proyecto para que su proceso sea fácil y sencillo.

# Documentación
La documentación con respecto al PDF original de AZUL se encuentra en la siguiente [wiki](https://github.com/mrjeanp/azul-php/wiki) para saber más sobre AZUL Webservices.

Si desean probar los servicios directamente con Postman se puede hacer [aquí](https://documenter.getpostman.com/view/4535087/SWEB3c39?version=latest).

*Gracias a: [Mrjeanp](https://github.com/mrjeanp/azul-php) Por la documentación y los servicios en Postman.*

# Instalación y uso
1. Instale PHP 7.* y [Composer](https://getcomposer.org/doc/00-intro.md).
2. Clone este proyecto `git clone https://github.com/supermavster/php-azul-payment-gateway.git`.
3. Ingrese a la carpeta *php-azul-payment-gateway*.
4. Ejecute `composer install`.
5. Configure los archivos dados por Azul, tales como credenciales y keys.
   a. Digirse al archivo [Config.json](https://github.com/supermavster/php-azul-payment-gateway/blob/master/cert/Config.json):
      ```
      {
          "debug": true,
          "publicPath": "ABSOLUTE_PATH_CERT_LOCATION",
          "typeService": "JSON",
          "contentType": "application/json",
          "customerServicePhone": "PHONE",
          "eCommerceUrl": "WEBSITE",
          "test" : {
              "url": "pruebas",
              "store": STORE_ID,
              "merchantId": MERCHAN_ID,
              "authOne"       : "AUTH_USER",
              "authTwo"       : "AUTH_PASSWORD"
          },
          "production":{
              "url": "pagos",
              "store": STORE_ID,
              "merchantId": MERCHAN_ID,
              "authOne"       : "AUTH_USER",
              "authTwo"       : "AUTH_PASSWORD"
          }
      }
      ```
      
      **Explicación de TAGS:**
      
      | Key                         | Description                                                                                    |
      |-----------------------------|------------------------------------------------------------------------------------------------|
      | ABSOLUTE_PATH_CERT_LOCATION | Ubicación de la carpeta donde se encuentran los certificados como azul.pem, azul.key, azul.scr |
      | PHONE                       | Número de teléfono                                                                             |
      | WEBSITE                     | Sitio Web Registrado en Azul                                                                   |
      | STORE_ID                    | Valor dado por Azul identificado como Store                                                    |
      | MERCHAN_ID                  | Valor dado por Azul identificado como MerchantId, de no encontrarse es el mismo que Store      |
      | AUTH_USER                   | Valor dado por Azul identificado como Auth1                                                    |
      | AUTH_PASSWORD               | Valor dado por Azul identificado como Auth2                                                    |
      
      b. Se debe tener en cuenta que en la carpeta [cert](https://github.com/supermavster/php-azul-payment-gateway/tree/master/cert) existen dos folders `test` y `production` en ellos deben de poner los archivos dados por Azul, tal como azul.key y azul.csr o de ser azul.pem con permisos
      
      `chmod 664 azul.key` y `chmod 664 azul.csr`
6. Ejecute el archivo index.php con el comando `php -f index.php`.
7. Disfrute los resultados.

# Resultado de las pruebas (TESTING)

### Realizar 2 transacciones aprobadas con tarjetas y montos distintos (Sale)
```
[
  {
    "AuthorizationCode": "OK8257",
    "AzulOrderId": "43427",
    "CustomOrderId": "SALE-1",
    "DateTime": "20200415124718",
    "ErrorDescription": "",
    "IsoCode": "00",
    "LotNumber": "",
    "RRN": "20200415124721592312",
    "ResponseCode": "ISO8583",
    "ResponseMessage": "APROBADA",
    "Ticket": "59"
  },
  {
    "AuthorizationCode": "OK8287",
    "AzulOrderId": "43428",
    "CustomOrderId": "SALE-2",
    "DateTime": "20200415124722",
    "ErrorDescription": "",
    "IsoCode": "00",
    "LotNumber": "",
    "RRN": "20200415124725640525",
    "ResponseCode": "ISO8583",
    "ResponseMessage": "APROBADA",
    "Ticket": "60"
  }
]
```
### Consultar ambas transacciones aprobadas (VerifyPayment)
```
[
  {
    "Amount": "650730",
    "AuthorizationCode": "OK8257",
    "CardNumber": "411113******6504",
    "CurrencyPosCode": "$",
    "CustomOrderId": "SALE-1",
    "DateTime": "20200415124718",
    "ErrorDescription": "",
    "Found": true,
    "IsoCode": "00",
    "Itbis": "99264",
    "LotNumber": "2",
    "RRN": "20200415124721592312",
    "ResponseCode": "ISO8583",
    "Ticket": "59"
  },
  {
    "Amount": "100000",
    "AuthorizationCode": "OK8287",
    "CardNumber": "426257******4496",
    "CurrencyPosCode": "$",
    "CustomOrderId": "SALE-2",
    "DateTime": "20200415124722",
    "ErrorDescription": "",
    "Found": true,
    "IsoCode": "00",
    "Itbis": "50000",
    "LotNumber": "2",
    "RRN": "20200415124725640525",
    "ResponseCode": "ISO8583",
    "Ticket": "60"
  }
]
```
### Anular ambas transacciones aprobadas (Void)
```
[
  {
    "AuthorizationCode": "OK8257",
    "AzulOrderId": "43429",
    "CustomOrderId": "",
    "DataVaultBrand": "",
    "DataVaultExpiration": "",
    "DataVaultToken": "",
    "DateTime": "20200415124729",
    "ErrorDescription": "",
    "IsoCode": "00",
    "LotNumber": "",
    "RRN": "20200415124731335810",
    "ResponseCode": "ISO8583",
    "ResponseMessage": "APROBADA",
    "Ticket": "61"
  },
  {
    "AuthorizationCode": "OK8287",
    "AzulOrderId": "43430",
    "CustomOrderId": "",
    "DataVaultBrand": "",
    "DataVaultExpiration": "",
    "DataVaultToken": "",
    "DateTime": "20200415124732",
    "ErrorDescription": "",
    "IsoCode": "00",
    "LotNumber": "",
    "RRN": "20200415124734092948",
    "ResponseCode": "ISO8583",
    "ResponseMessage": "APROBADA",
    "Ticket": "62"
  }
]
```
### Realizar 2 retenciones con tarjetas y montos distintos (Hold)
```
[
  {
    "AuthorizationCode": "OK8327",
    "AzulOrderId": "43431",
    "CustomOrderId": "HOLD-1",
    "DateTime": "20200415124735",
    "ErrorDescription": "",
    "IsoCode": "00",
    "LotNumber": "",
    "RRN": "20200415124738109159",
    "ResponseCode": "ISO8583",
    "ResponseMessage": "APROBADA",
    "Ticket": "63"
  },
  {
    "AuthorizationCode": "OK8357",
    "AzulOrderId": "43432",
    "CustomOrderId": "HOLD-2",
    "DateTime": "20200415124739",
    "ErrorDescription": "",
    "IsoCode": "00",
    "LotNumber": "",
    "RRN": "20200415124742165356",
    "ResponseCode": "ISO8583",
    "ResponseMessage": "APROBADA",
    "Ticket": "64"
  }
]
```
### Realizar el posteo de las 2 transacciones retenidas (Post)
```
[
  {
    "AuthorizationCode": "OK8327",
    "AzulOrderId": "43433",
    "CustomOrderId": "",
    "DataVaultBrand": "",
    "DataVaultExpiration": "",
    "DataVaultToken": "",
    "DateTime": "20200415124743",
    "ErrorDescription": "",
    "IsoCode": "00",
    "LotNumber": "",
    "RRN": "",
    "ResponseCode": "ISO8583",
    "ResponseMessage": "APROBADA",
    "Ticket": ""
  },
  {
    "AuthorizationCode": "OK8357",
    "AzulOrderId": "43434",
    "CustomOrderId": "",
    "DataVaultBrand": "",
    "DataVaultExpiration": "",
    "DataVaultToken": "",
    "DateTime": "20200415124746",
    "ErrorDescription": "",
    "IsoCode": "00",
    "LotNumber": "",
    "RRN": "",
    "ResponseCode": "ISO8583",
    "ResponseMessage": "APROBADA",
    "Ticket": ""
  }
]
```
### Crear 2 tokens con tarjetas (Data Vault Create)
```
[
  {
    "Brand": "VISA",
    "CardNumber": "411113...6504",
    "DataVaultToken": "115EFC8F-C5B3-4A23-91A3-1E6B7EBBEFA6",
    "ErrorDescription": "",
    "Expiration": "202206",
    "HasCVV": true,
    "IsoCode": "00",
    "ResponseMessage": "APROBADA"
  },
  {
    "Brand": "VISA",
    "CardNumber": "426257...4496",
    "DataVaultToken": "D0664B05-DFE9-4633-9AC8-88BB1D8D92D5",
    "ErrorDescription": "",
    "Expiration": "202708",
    "HasCVV": true,
    "IsoCode": "00",
    "ResponseMessage": "APROBADA"
  }
]
```
### Realizar 2 transacciones con ambos tokens (Data Vault Sale)
```
[
  {
    "AuthorizationCode": "OK8387",
    "AzulOrderId": "43435",
    "CustomOrderId": "",
    "DateTime": "20200415124753",
    "ErrorDescription": "",
    "IsoCode": "00",
    "LotNumber": "",
    "RRN": "20200415124756379080",
    "ResponseCode": "ISO8583",
    "ResponseMessage": "APROBADA",
    "Ticket": "65"
  },
  {
    "AuthorizationCode": "OK8407",
    "AzulOrderId": "43436",
    "CustomOrderId": "",
    "DateTime": "20200415124757",
    "ErrorDescription": "",
    "IsoCode": "00",
    "LotNumber": "",
    "RRN": "20200415124800314284",
    "ResponseCode": "ISO8583",
    "ResponseMessage": "APROBADA",
    "Ticket": "66"
  }
]
```
### Eliminar ambos tokens creados (Data Vault Delete)
```
[
  {
    "Brand": "",
    "CardNumber": "",
    "DataVaultToken": "",
    "ErrorDescription": "",
    "Expiration": "",
    "HasCVV": false,
    "IsoCode": "00",
    "ResponseMessage": "APROBADA"
  },
  {
    "Brand": "",
    "CardNumber": "",
    "DataVaultToken": "",
    "ErrorDescription": "",
    "Expiration": "",
    "HasCVV": false,
    "IsoCode": "00",
    "ResponseMessage": "APROBADA"
  }
]
```
