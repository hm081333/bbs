<?php

return [
    // 默认使用
    'default' => env('encrypt.driver', 'openssl'),

    // 配置信息
    'stores' => [
        'openssl' => [
            // 驱动方式
            'type' => 'OpenSSL',
            // 加密算法，使用openssl_get_cipher_methods()函数获取可用的加密算法列表。
            'cipher_algo' => 'AES-256-CFB',
            // 口令（passphrase）。 若 passphrase 比预期长度短，将静默用 NUL 填充； 若比预期长度更长，将静默截断。
            'passphrase' => '@fdskalhfj2387A!',
            // options 是以下标记的按位或： OPENSSL_RAW_DATA 、 OPENSSL_ZERO_PADDING。
            'options' => OPENSSL_ZERO_PADDING,
            // 非 NULL 的初始化向量。
            'iv' => '@fdfpu+adj2387A!',
            // 使用 AEAD 密码模式（GCM 或 CCM）时传引用的验证标签。 如果是错误的，验证失败，函数返回false.
            'tag' => null,
            // 验证 tag 的长度。GCM 模式时，它的范围是 4 到 16。
            'tag_length' => 16,
            // 附加的验证数据。
            'aad' => '',
        ],
        // 更多的缓存连接
    ],
];
