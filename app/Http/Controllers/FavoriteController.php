<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Получение списка фильмов добавленных пользователем в избранное
     */
    public function index()
    {
    }

    /**
     * Добавление фильма в избранное
     */
    public function store(Request $request, string $id)
    {
    }

    /**
     * Удаление фильма из избранного
     */
    public function destroy(string $id)
    {
    }
}
