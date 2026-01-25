<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['title' => 'Текст', 'description' => 'Свободные текстовые поля: имена, описания, комментарии.'],
            ['title' => 'Число', 'description' => 'Числовые значения без валюты: количество, счетчики, метрики.'],
            ['title' => 'Дата', 'description' => 'Даты и сроки: создание, дедлайн, период.'],
            ['title' => 'Булево', 'description' => 'Логические значения: да/нет, включено/выключено.'],
            ['title' => 'Категория', 'description' => 'Простая категория без строгого списка значений.'],
            ['title' => 'Финансовые данные', 'description' => 'Суммы, валюты, проценты, налоги, бюджеты.'],
            ['title' => 'Контактные данные', 'description' => 'Email, телефон, адрес, компания, должность.'],
            ['title' => 'Статус / Категория (Enum)', 'description' => 'Фиксированный список значений: новый/в работе/закрыт.'],
            ['title' => 'Ссылки (URL/Path)', 'description' => 'URL‑адреса, пути к файлам, ссылки на документы.'],
            ['title' => 'Геоданные', 'description' => 'Координаты, города, страны, регионы, адреса.'],
        ];

        foreach ($types as $type) {
            Type::firstOrCreate(
                ['title' => $type['title']],
                ['description' => $type['description']]
            );
        }
    }
}
