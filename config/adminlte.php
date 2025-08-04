<?php



return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'SGCR - PRODUÇÃO',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => true,
    'use_full_favicon' => true,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>IPEN</b>&nbsp;SGCR',
    'logo_img' => 'vendor/adminlte/dist/img/logo_novopP1.png',
    'logo_img_class' => 'brand-image img-circle elevation-4',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'null',
    'logo_img_alt' => 'Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/logo_novopP1.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/logo_novopP1.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => false,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => true,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => 'd-nones',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-danger',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => 'p-1 mb-2 bg-info text-blue rounded',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-light-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'ipen-head navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        [
            'text' => 'PSP-PS',
            'url'  => '/psp-ps',
            'icon' => 'fas fa-folder',
            'can'  => 'view-psp-ps'
        ],
        // Navbar items:
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:

        [
            'text' => 'Produção RL',
            'icon' => 'fas fa-industry',
            'submenu' => [
                [
                    'text' => 'Folha de Produção',
                    'url' => 'prl-fp',
                    'icon' => 'fas fa-clipboard-list',
                ],
                [
                    'text' => 'Altera Estoque',
                    'url' => 'prl-ae',
                    'icon' => 'fas fa-boxes',
                ],

            ],
        ],
        [
            'text' => 'Folha de Prod.-Embalado',
            'url' => 'fpe',
            'icon' => 'fas fa-box-open',
        ],
        [
            'text' => 'Transferências',
            'icon' => 'fas fa-exchange-alt',
            'submenu' => [
                [
                    'text' => 'Transf. Material',
                    'url' => 'trf-tm',
                    'icon' => 'fas fa-truck',
                ],
                [
                    'text' => 'Transf. Material-Doação',
                    'url' => 'trf-td',
                    'icon' => 'fas fa-gift',
                ],
                [
                    'text' => 'Consulta',
                    'url' => 'trf-co',
                    'icon' => 'fas fa-search',
                ],
                [
                    'text' => 'Altera Estoque Inicial',
                    'url' => 'trf-ai',
                    'icon' => 'fas fa-edit',
                ],
            ],
        ],
        [
            'text' => 'Definição & Verificação',
            'icon' => 'fas fa-cogs',
            'submenu' => [
                [
                    'text' => 'Definição de Série',
                    'url' => 'dfv-ds',
                    'icon' => 'fas fa-list-ol',
                ],
                [
                    'text' => 'Lista de Verificação',
                    'url' => 'dfv-lv',
                    'icon' => 'fas fa-clipboard-check',
                ],
                [
                    'text' => 'Registro de Limpeza',
                    'url' => 'dfv-rl',
                    'icon' => 'fas fa-broom',
                ],
                [
                    'text' => 'Dados de Produção',
                    'url' => 'dfv-dp',
                    'icon' => 'fas fa-chart-bar',
                ],
                [
                    'text' => 'Dados de Produção - Intervalo',
                    'url' => 'dfv-di',
                    'icon' => 'fas fa-chart-line',
                ],
            ],
        ],

        [
            'text' => 'Folha de Produção MM',
            'url' => 'fpm',
            'icon' => 'fas fa-key',
        ],
        [
            'text' => 'Planej. Produção RP',
            'url' => 'ppr',
            'icon' => 'fas fa-calendar-alt',
        ],
        [
            'text' => 'Distribuição RP',
            'icon' => 'fas fa-share-alt',
            'submenu' => [
                [
                    'text' => 'Gálio',
                    'url' => 'drp-ga',
                    'icon' => 'fas fa-atom',
                ],
                [
                    'text' => 'Gerador',
                    'url' => 'drp-ge',
                    'icon' => 'fas fa-cog',
                ],
                [
                    'text' => 'Iodo',
                    'url' => 'drp-io',
                    'icon' => 'fas fa-radiation',
                ],
                [
                    'text' => 'Tálio',
                    'url' => 'drp-tl',
                    'icon' => 'fas fa-flask',
                ],
            ],
        ],
        [
            'text' => 'Pastas de Produção',
            'icon' => 'fas fa-folder-open',
            'submenu' => [
                [
                    'text' => 'Altera Datas',
                    'url' => 'psp-ad',
                    'icon' => 'fas fa-calendar',
                ],
                [
                    'text' => 'R.D. & M.M.',
                    'url' => 'psp-rm',
                    'icon' => 'fas fa-file-medical',
                ],
                [
                    'text' => 'Reagentes Liofilizados',
                    'url' => 'psp-rl',
                    'icon' => 'fas fa-snowflake',
                ],
                [
                    'text' => 'Pastas',
                    'url' => 'psp-ps',
                    'icon' => 'fas fa-folder',
                ],

                [
                    'text' => 'Pastas Não Concluídas',
                    'url' => 'psp-pc',
                    'icon' => 'fas fa-folder-plus',
                ],
                [
                    'text' => 'Pastas Não Impressas',
                    'url' => 'psp-pi',
                    'icon' => 'fas fa-folder-minus',
                ],
            ],
        ],
        [
            'text' => 'Embalagem',
            'icon' => 'fas fa-box',
            'submenu' => [
                [
                    'text' => 'Folha de Embalagem',
                    'url' => 'emb-fe',
                    'icon' => 'fas fa-clipboard-list',
                ],
                [
                    'text' => 'Registra Lote & Castelo',
                    'url' => 'emb-ls',
                    'icon' => 'fas fa-tags',
                ],
                [
                    'text' => 'Recebimento de Blindagens',
                    'url' => 'emb-rb',
                    'icon' => 'fas fa-shield-alt',
                ],
                [
                    'text' => 'Recebimento de Blindagens - Autorização',
                    'url' => 'emb-ra',
                    'icon' => 'fas fa-user-check',
                ],
                [
                    'text' => 'Retorno do Monobloco',
                    'url' => 'emb-rm',
                    'icon' => 'fas fa-undo',
                ],
                [
                    'text' => 'Gelo Seco',
                    'url' => 'emb-gs',
                    'icon' => 'fas fa-snowflake',
                ],
                [
                    'text' => 'Registra Anel & Disco',
                    'url' => 'emb-ad',
                    'icon' => 'fas fa-circle',
                ],
            ],
        ],
        [
            'text' => 'Reemissão Documentos',
            'icon' => 'fas fa-file-alt',
            'submenu' => [
                [
                    'text' => 'Alterar Dose de Superfície',
                    'url' => 'rdc-ds',
                    'icon' => 'fas fa-radiation-alt',
                ],
                [
                    'text' => 'Guia de Monitoração',
                    'url' => 'rdc-gm',
                    'icon' => 'fas fa-clipboard-check',
                ],
                [
                    'text' => 'Folha de Dados',
                    'url' => 'rdc-fd',
                    'icon' => 'fas fa-file-medical',
                ],
                [
                    'text' => 'Ficha de Emergência',
                    'url' => 'rdc-fe',
                    'icon' => 'fas fa-exclamation-triangle',
                ],
                [
                    'text' => 'Boleto',
                    'url' => 'rdc-bl',
                    'icon' => 'fas fa-receipt',
                ],
                [
                    'text' => 'Conjunto Completo',
                    'url' => 'rdc-cc',
                    'icon' => 'fas fa-receipt',
                ],
            ],
        ],

        [
            'text' => 'Blindagem X Pasta',
            'url' => 'bxp',
            'icon' => 'fas fa-star',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'vendor/datatables/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/datatables/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/select2/js/select2.full.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/select2/css/select2.min.css',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css',
                ],
            ],
        ],
        'Toasts' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js
                    ',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],

        'DatatablesPlugins' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/buttons/js/dataTables.buttons.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/buttons/js/buttons.bootstrap4.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/buttons/js/buttons.html5.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/buttons/js/buttons.print.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/jszip/jszip.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/pdfmake/pdfmake.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/pdfmake/vfs_fonts.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/buttons/css/buttons.bootstrap4.min.css',
                ],
            ],
        ],


        'DateRangePicker' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/moment/moment.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.js',
                ],

                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.css',
                ],
            ],
        ],

        'Summernote' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    //'location' => 'vendor/summernote/summernote-bs4.min.js',
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.16/summernote-bs4.min.js',
                    //'location' =>  'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.16/lang/summernote-pt-BR.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    //'location' => 'vendor/summernote/summernote-bs4.min.css',
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.16/summernote-bs4.min.css',
                ],
            ],

            'iCheckBootstrap' => [
                    'active' => true,
                    'files' => [
                        [
                            'type' => 'css',
                            'asset' => true,
                            'location' => 'vendor/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
                        ],
                    ],
                ],
            ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
