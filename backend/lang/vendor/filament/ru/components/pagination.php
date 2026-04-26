<?php

return [

    'label' => 'Пагинация',

    'overview' => 'Показаны записи с :first по :last из :total',

    'fields' => [

        'records_per_page' => [

            'label' => 'Записей на странице',

            'options' => [
                'all' => 'Все',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Первая',
        ],

        'go_to_page' => [
            'label' => 'Перейти к странице :page',
        ],

        'last' => [
            'label' => 'Последняя',
        ],

        'next' => [
            'label' => 'Следующая',
        ],

        'previous' => [
            'label' => 'Предыдущая',
        ],

    ],

];
