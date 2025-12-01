<?php
namespace App\Interfaces;
use App\Models\Author;
interface AuthorRepositoryInterface
{
   public function paginate(int $per_page);
   public function find(int $id);
   public function create(array $data);
   public function update(int $id, array $data);
   public function delete(int $id);
   public function isAuthorHasBooks(int $id);
}