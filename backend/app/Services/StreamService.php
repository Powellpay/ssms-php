<?php

namespace App\Services;

use App\Models\Stream;
use App\Repositories\Contracts\StreamRepositoryInterface;
use App\Services\Contracts\StreamServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class StreamService implements StreamServiceInterface
{
    public function __construct(
        protected StreamRepositoryInterface $streamRepository
    ) {}

    public function all(): Collection
    {
        return $this->streamRepository->all();
    }

    public function find(int $id): ?Stream
    {
        return $this->streamRepository->find($id);
    }

    public function create(array $data): Stream
    {
        return $this->streamRepository->create($data);
    }

    public function update(int $id, array $data): Stream
    {
        return $this->streamRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->streamRepository->delete($id);
    }

    public function findByClassLevel(int $levelId): Collection
    {
        return Stream::where('class_level_id', $levelId)->get();
    }

    public function findByAcademicYear(int $yearId): Collection
    {
        return Stream::where('academic_year_id', $yearId)->get();
    }
}
