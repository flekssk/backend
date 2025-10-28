<?php

declare(strict_types=1);

namespace App\Services\Slots\Actions;

use App\Services\Slots\Http\Requests\SlotsListRequest;
use App\Services\Slots\Repositories\SlotRepository;
use FKS\Actions\Action;
use FKS\Search\Collections\EntitiesCollection;
use FKS\Search\ValueObjects\SearchConditions;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class SlotsListAction extends Action
{
    public function __construct(private readonly SlotRepository $slotRepository)
    {
    }

    public function handle(SearchConditions $conditions): EntitiesCollection|Builder|bool|Collection|null
    {
        return $this->slotRepository->search($conditions);
    }

    public function asController(SlotsListRequest $request): Response|JsonResponse
    {
        return response()->json($this->handle($request->getSearchConditions())->map->only($request->getAvailableFields()));
    }
}
