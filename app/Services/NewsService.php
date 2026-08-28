<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\News;
use App\Repositories\NewsRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsService
{
    public function __construct(
        private readonly NewsRepository $repository
    ) {}

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function findByIdOrFail(int $id): News
    {
        return $this->repository->findByIdOrFail($id);
    }

    public function create(array $data, ?UploadedFile $thumbnail = null): News
    {
        if ($thumbnail) {
            $data['thumbnail'] = ImageHelper::upload($thumbnail, 'news');
        }

        if (($data['status'] ?? '') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $this->repository->create($data);
    }

    public function update(News $news, array $data, ?UploadedFile $thumbnail = null): void
    {
        if ($thumbnail) {
            $data['thumbnail'] = ImageHelper::replace($thumbnail, $news->thumbnail, 'news');
        }

        if (($data['status'] ?? '') === 'published' && empty($news->published_at) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $this->repository->update($news, $data);
    }

    public function delete(News $news): void
    {
        ImageHelper::delete($news->thumbnail);
        $this->repository->delete($news);
    }
}
