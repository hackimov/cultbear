<?php

return [

    'column_toggle' => [

        'heading' => 'Столбцы',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Скрыть :count',
                'expand_list' => 'Показать ещё :count',
            ],

            'more_list_items' => 'и ещё :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Выбрать или снять выделение со всех записей для массовых действий.',
        ],

        'bulk_select_record' => [
            'label' => 'Выбрать или снять выделение записи :key для массовых действий.',
        ],

        'bulk_select_group' => [
            'label' => 'Выбрать или снять выделение группы «:title» для массовых действий.',
        ],

        'search' => [
            'label' => 'Поиск',
            'placeholder' => 'Поиск',
            'indicator' => 'Поиск',
        ],

    ],

    'summary' => [

        'heading' => 'Сводка',

        'subheadings' => [
            'all' => 'Все :label',
            'group' => 'Сводка :group',
            'page' => 'Эта страница',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Среднее',
            ],

            'count' => [
                'label' => 'Кол.',
            ],

            'sum' => [
                'label' => 'Сумма',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Сохранить порядок',
        ],

        'enable_reordering' => [
            'label' => 'Изменить порядок',
        ],

        'filter' => [
            'label' => 'Фильтр',
        ],

        'group' => [
            'label' => 'Группировать',
        ],

        'open_bulk_actions' => [
            'label' => 'Открыть действия',
        ],

        'toggle_columns' => [
            'label' => 'Переключить столбцы',
        ],

    ],

    'empty' => [

        'heading' => ':model не найдены',

        'description' => 'Чтобы добавить запись, нажмите кнопку «Создать».',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Применить фильтры',
            ],

            'remove' => [
                'label' => 'Удалить фильтр',
            ],

            'remove_all' => [
                'label' => 'Очистить фильтры',
                'tooltip' => 'Очистить фильтры',
            ],

            'reset' => [
                'label' => 'Сбросить',
            ],

        ],

        'heading' => 'Фильтры',

        'indicator' => 'Активные фильтры',

        'multi_select' => [
            'placeholder' => 'Все',
        ],

        'select' => [
            'placeholder' => 'Все',
        ],

        'trashed' => [

            'label' => 'Удалённые записи',

            'only_trashed' => 'Только удалённые',

            'with_trashed' => 'С удалёнными',

            'without_trashed' => 'Без удалённых',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Группировать по',
                'placeholder' => 'Группировать по',
            ],

            'direction' => [

                'label' => 'Направление',

                'options' => [
                    'asc' => 'По возрастанию',
                    'desc' => 'По убыванию',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Перетаскивайте записи, чтобы изменить порядок.',

    'selection_indicator' => [

        'selected_count' => 'В выделении: :count',

        'actions' => [

            'select_all' => [
                'label' => 'Выбрать все (:count)',
            ],

            'deselect_all' => [
                'label' => 'Снять выделение со всех записей',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Сортировка',
            ],

            'direction' => [

                'label' => 'Направление',

                'options' => [
                    'asc' => 'По возрастанию',
                    'desc' => 'По убыванию',
                ],

            ],

        ],

    ],

];
