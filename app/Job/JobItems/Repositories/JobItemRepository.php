<?php

namespace App\Job\JobItems\Repositories;

use App\Job\Categories\Category;
use App\Job\JobItems\JobItem;
use Jsdecena\Baserepo\BaseRepository;
use App\Job\JobItems\Repositories\Interfaces\JobItemRepositoryInterface;
use App\Job\JobItems\Exceptions\JobItemNotFoundException;
use App\Job\JobItems\Exceptions\AppliedJobItemNotFoundException;
use App\Job\JobItems\Exceptions\JobItemCreateErrorException;
use App\Job\JobItems\Exceptions\JobItemUpdateErrorException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Image;
use Storage;
use File;

class JobItemRepository extends BaseRepository implements JobItemRepositoryInterface
{
    /**
     * JobItemRepository constructor.
     * @param JobItem $jobItem
     */
    public function __construct(JobItem $jobItem)
    {
        parent::__construct($jobItem);
        $this->model = $jobItem;
    }

    /**
     * List all the jobitems
     *
     * @param string $order
     * @param string $sort
     * @param array $columns
     * @param string $active
     * @return Collection
     */
    public function listJobItems(string $order = 'id', string $sort = 'desc', array $columns = ['*'], string $active = 'on'): Collection
    {
        if ($active === 'on') {
            return $this->model->ActiveJobitem()->orderBy($order, $sort)->get($columns);
        } elseif ($active === 'off') {
            return $this->model->orderBy($order, $sort)->get($columns);
        }
    }

    /**
     * count active jobitems
     *
     * @return integer
     */
    public function listJobitemCount(): int
    {
        return $this->model->ActiveJobitem()->count();
    }

    /**
     * Create the jobitem
     *
     * @param array $data
     *
     * @return JobItem
     * @throws JobItemCreateErrorException
     */
    public function createJobItem(array $data): JobItem
    {
        try {
            return $this->create($data);
        } catch (QueryException $e) {
            throw new JobItemCreateErrorException($e);
        }
    }

    /**
     * @param array $data
     * @return bool
     *
     * @throws JobItemUpdateErrorException
     */
    public function updateJobItem(array $data): bool
    {
        try {
            return $this->model->where('id', $this->model->id)->update($data);
        } catch (QueryException $e) {
            throw new JobItemUpdateErrorException($e);
        }
    }

    /**
     * @param int $applyId
     * @param array $data
     * @return bool
     */
    public function updateAppliedJobItem(int $applyId,  array $data): bool
    {
        return $this->model->applies()->updateExistingPivot($applyId, $data);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function searchJobItem(array $data = [], string $orderBy = 'created_at', string $sortBy = 'desc', $columns = ['*']): Collection
    {
        if ($data !== []) {
            return $this->queryBy($this->model::query(), $data)->orderBy($orderBy, $sortBy)->get($columns);
        } else {
            return $this->listJobItems($orderBy, $sortBy, ['*'], 'off');
        }
    }

    /**
     * create recent jobitems id list
     *
     * @param array $req
     * @param integer $id
     * @return void
     */
    public function createRecentJobItemIdList($req, int $id): void
    {
        if (session()->has('recent_jobs') && is_array(session()->get('recent_jobs'))) {

            $historyLimit = '';
            $jobitem_id_list = array(
                'limit_list' => [],
                'all_list' => []
            );

            $jobitem_id_list['limit_list'] = session()->get('recent_jobs.limit_list');
            $jobitem_id_list['all_list'] = session()->get('recent_jobs.all_list');

            $deviceFrag = $this->model->isMobile($req);
            switch ($deviceFrag) {
                case 'pc':
                    $historyLimit = 5;
                    break;
                case 'mobile':
                    $historyLimit = 3;
                    break;
                default:
                    $historyLimit = '';
                    break;
            }

            foreach ($jobitem_id_list as $listKey => $idList) {
                if ($listKey === 'limit_list') {
                    if (in_array($id, $idList) == false) {
                        if (count($idList) >= $historyLimit) {
                            array_shift($idList);
                        }
                        array_push($idList, $id);
                    } else {
                        while (($index = array_search($id, $idList)) !== false) {
                            unset($idList[$index]);
                        };
                        array_push($idList, $id);
                    }

                    session()->put('recent_jobs.limit_list', $idList);
                } else {
                    if (in_array($id, $idList) == false) {
                        session()->push('recent_jobs.all_list', $id);
                    } else {
                        while (($index = array_search($id, $idList)) !== false) {
                            unset($idList[$index]);
                        };
                        array_push($idList, $id);
                        session()->put('recent_jobs.all_list', $idList);
                    }
                }
            }
        } else {
            session()->push('recent_jobs.limit_list', $id);
            session()->push('recent_jobs.all_list', $id);
        }
    }

    /**
     *  list recent jobitems id
     *
     * @return LengthAwarePaginator|Collection|array
     */
    public function listRecentJobItemId(int $historyFlag = 0)
    {
        $jobitem_id_list = [];
        switch ($historyFlag) {
            case 0:
                if (session()->has('recent_jobs.limit_list') && is_array(session()->get('recent_jobs.limit_list'))) {
                    $jobitem_id_list = session()->get('recent_jobs.limit_list');
                }
                break;
            case 1:
                if (session()->has('recent_jobs.all_list') && is_array(session()->get('recent_jobs.all_list'))) {
                    $jobitem_id_list = session()->get('recent_jobs.all_list');
                }
                break;
        }

        if ($jobitem_id_list !== []) {
            $jobitem_id_rv_list = array_reverse($jobitem_id_list);
            $placeholder = '';
            foreach ($jobitem_id_rv_list as $key => $value) {
                $placeholder .= ($key == 0) ? $value : ',' . $value;
            }

            if ($historyFlag === 0) {
                return $this->model->whereIn('id', $jobitem_id_rv_list)->orderByRaw("FIELD(id, $placeholder)", $jobitem_id_rv_list)->get();
            } elseif ($historyFlag === 1) {
                return $this->model->whereIn('id', $jobitem_id_rv_list)->orderByRaw("FIELD(id, $placeholder)", $jobitem_id_rv_list)->paginate(20);
            }
        } else {
            return $jobitem_id_list;
        }
    }

    /**
     * base search the jobitems
     *
     * @param string $searchParam
     * @return $query
     */
    public function baseSearchJobItems(array $searchParam = [])
    {
        $query = $this->model->activeJobitem()->with([
            'categories'
        ]);

        $newsearchParam = $searchParam;

        foreach ($newsearchParam as $key => $p) {
            if ($p === null) {
                unset($newsearchParam[$key]);
            }
        }

        if ($newsearchParam !== []) {
            $query->search($searchParam);
        } else {
            $query->latest();
        }

        return $query;
    }

    /**
     * Find the active jobitem by ID
     *
     * @return Collection|JobItem
     * @throws JobItemNotFoundException
     */
    public function findJobItemById($id)
    {
        try {
            return $this->model->ActiveJobitem()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new JobItemNotFoundException($e);
        }
    }

    /**
     * Find the jobitem by ID
     *
     * @return Collection|JobItem
     * @throws JobItemNotFoundException
     */
    public function findAllJobItemById($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new JobItemNotFoundException($e);
        }
    }

    /**
     * @param JobItem $jobitem
     * @return array
     */
    public function savedDbFilePath(JobItem $jobitem): array
    {
        $savedFilePath = [];
        $fileSessionKeys = config('const.FILE_SLUG');

        foreach ($fileSessionKeys as $fileSessionKey) {
            switch ($fileSessionKey) {
                case 'main':
                    $savedFilePath['image'][$fileSessionKey] = $jobitem->job_img;
                    $savedFilePath['movie'][$fileSessionKey] = $jobitem->job_mov;
                    break;
                case 'sub1':
                    $savedFilePath['image'][$fileSessionKey] = $jobitem->job_img2;
                    $savedFilePath['movie'][$fileSessionKey] = $jobitem->job_mov2;
                    break;
                case 'sub2':
                    $savedFilePath['image'][$fileSessionKey] = $jobitem->job_img3;
                    $savedFilePath['movie'][$fileSessionKey] = $jobitem->job_mov3;
                    break;
            }
        }

        return $savedFilePath;
    }

    /**
     * @param void
     *
     * @return $jobImageBaseUrl
     */
    public function getJobBaseUrl(): string
    {
        $jobBaseUrl = '';
        if (config('app.env') == 'production') {
            $jobBaseUrl = config('app.s3_url');
        } else {
            $jobBaseUrl = config('app.s3_url_local');
        }

        return $jobBaseUrl;
    }

    /**
     * @param array $category
     */
    public function associateCategory(array $category)
    {
        $this->model->categories()->attach($category['id'], [
            'ancestor_id' => $category['ancestor_id'],
            'ancestor_slug' => $category['ancestor_slug'],
            'parent_id' => $category['parent_id'],
            'parent_slug' => $category['parent_slug'],
        ]);
    }

    /**
     * @param int $categoryId
     */
    public function dissociateCategory(int $categoryId)
    {
        $this->model->categories()->detach($categoryId);
    }
}
