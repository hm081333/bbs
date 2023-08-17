<?php

use BeyondCode\LaravelWebSockets\Dashboard\Http\Middleware\Authorize;

return [

    /*
     * 设置自定义仪表板配置
     */
    'dashboard' => [
        'port' => env('LARAVEL_WEBSOCKETS_PORT', 6001),
    ],

    /*
     * 该软件包具有开箱即用的多租户功能。 您可以在此处配置可以使用 webSockets 服务器的不同应用程序。
     *
     * 您可以选择指定容量，以便限制特定应用程序的最大并发连接数。
     *
     * 您可以选择禁用客户端事件，以便客户端无法通过 webSockets 相互发送消息。
     */
    'apps' => [
        [
            'id' => env('PUSHER_APP_ID'),
            'name' => env('APP_NAME'),
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'path' => env('PUSHER_APP_PATH'),
            'capacity' => null,
            'enable_client_messages' => false,
            'enable_statistics' => true,
        ],
    ],

    /*
     * 此类负责查找应用程序。默认提供程序将使用此配置文件中定义的应用程序。
     *
     * 您可以通过实现“AppProvider”接口来创建自定义提供程序。
     */
    'app_provider' => BeyondCode\LaravelWebSockets\Apps\ConfigAppProvider::class,

    /*
     * 该数组包含您想要允许传入请求的主机。
     * 如果您想接受来自所有主机的请求，请将此项留空。
     */
    'allowed_origins' => [
        //
    ],

    /*
     * 传入 WebSocket 请求允许的最大请求大小（以千字节为单位）。
     */
    'max_request_size_in_kb' => 250,

    /*
     * 该路径将用于注册包的必要路由。
     */
    'path' => 'laravel-websockets',

    /*
     * 仪表板路由中间件
     *
     * 这些中间件将分配给每个仪表板路由，使您有机会将自己的中间件添加到此列表或更改任何现有的中间件。或者，您可以简单地坚持使用此列表。
     */
    'middleware' => [
        'web',
        Authorize::class,
    ],

    'statistics' => [
        /*
         * 该模型将用于存储 WebSocketsServer 的统计信息。
         * 唯一的要求是模型应该扩展此包提供的“WebSocketsStatisticsEntry”。
         */
        'model' => \BeyondCode\LaravelWebSockets\Statistics\Models\WebSocketsStatisticsEntry::class,

        /**
         * 默认情况下，统计记录器将处理传入的统计数据，存储它们，然后按照下面定义的每个时间间隔将它们释放到数据库中。
         */
        'logger' => BeyondCode\LaravelWebSockets\Statistics\Logger\HttpStatisticsLogger::class,

        /*
         * 您可以在此处指定记录统计信息的时间间隔（以秒为单位）。
         */
        'interval_in_seconds' => 60,

        /*
         * 执行 clean-命令时，所有早于此处指定天数的记录统计信息都将被删除。
         */
        'delete_statistics_older_than_days' => 60,

        /*
         * 使用 DNS 解析器向统计记录器发出请求，默认将所有内容解析为 127.0.0.1。
         */
        'perform_dns_lookup' => false,
    ],

    /*
     * 为您的 WebSocket 连接定义可选的 SSL 上下文。
     * 您可以在以下位置查看所有可用选项：http://php.net/manual/en/context.ssl.php
     */
    'ssl' => [
        /*
         * 文件系统上本地证书文件的路径。它必须是包含您的证书和私钥的 PEM 编码文件。它可以选择包含颁发者的证书链。私钥也可以包含在由 local_pk 指定的单独文件中。
         */
        'local_cert' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT', null),

        /*
         * 如果证书 (local_cert) 和私钥有单独的文件，则文件系统上本地私钥文件的路径。
         */
        'local_pk' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_PK', null),

        /*
         * local_cert 文件的密码。
         */
        'passphrase' => env('LARAVEL_WEBSOCKETS_SSL_PASSPHRASE', null),
    ],

    /*
     * 频道管理员
     * 此类处理通道持久性的处理方式。
     * 默认情况下，持久性由正在运行的 Web 服务器存储在数组中。
     * 唯一的要求是该类应该实现此包提供的“ChannelManager”接口。
     */
    'channel_manager' => \BeyondCode\LaravelWebSockets\WebSockets\Channels\ChannelManagers\ArrayChannelManager::class,
];
