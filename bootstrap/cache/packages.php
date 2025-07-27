<?php return array (
  'barryvdh/laravel-dompdf' => 
  array (
    'providers' => 
    array (
      0 => 'Barryvdh\\DomPDF\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'PDF' => 'Barryvdh\\DomPDF\\Facade',
    ),
  ),
  'daltcore/lara-pdf-merger' => 
  array (
    'providers' => 
    array (
      0 => 'LynX39\\LaraPdfMerger\\PdfMergerServiceProvider',
    ),
    'aliases' => 
    array (
      'PdfMerger' => 'LynX39\\LaraPdfMerger\\Facades\\PdfMerger',
    ),
  ),
  'facade/ignition' => 
  array (
    'aliases' => 
    array (
      'Flare' => 'Facade\\Ignition\\Facades\\Flare',
    ),
    'providers' => 
    array (
      0 => 'Facade\\Ignition\\IgnitionServiceProvider',
    ),
  ),
  'fideloper/proxy' => 
  array (
    'providers' => 
    array (
      0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'laravel/ui' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Ui\\UiServiceProvider',
    ),
  ),
  'milon/barcode' => 
  array (
    'providers' => 
    array (
      0 => 'Milon\\Barcode\\BarcodeServiceProvider',
    ),
    'aliases' => 
    array (
      'DNS1D' => 'Milon\\Barcode\\Facades\\DNS1DFacade',
      'DNS2D' => 'Milon\\Barcode\\Facades\\DNS2DFacade',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'niklasravnsborg/laravel-pdf' => 
  array (
    'providers' => 
    array (
      0 => 'niklasravnsborg\\LaravelPdf\\PdfServiceProvider',
    ),
    'aliases' => 
    array (
      'PDF' => 'niklasravnsborg\\LaravelPdf\\Facades\\Pdf',
    ),
  ),
  'nunomaduro/collision' => 
  array (
    'providers' => 
    array (
      0 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    ),
  ),
  'spatie/laravel-permission' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\Permission\\PermissionServiceProvider',
    ),
  ),
  'yajra/laravel-datatables-oracle' => 
  array (
    'aliases' => 
    array (
      'DataTables' => 'Yajra\\DataTables\\Facades\\DataTables',
    ),
    'providers' => 
    array (
      0 => 'Yajra\\DataTables\\DataTablesServiceProvider',
    ),
  ),
);