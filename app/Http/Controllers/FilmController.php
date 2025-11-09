<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FilmController extends Controller
{
    /**
     * Получение списка фильмов
     */
    public function index(Request $request, ?int $page, ?string $genre, ?string $status, ?string $order_by, ?string $order_to)
    {
    }

    /**
     * Добавление фильма в базу
     */
    public function store(Request $request)
    {
    }

    /**
     * Получение информации о фильме
     */
    public function show(string $id)
    {
    }

    /**
     * Редактирование фильма
     */
    public function update(Request $request, string $id)
    {
    }
}
