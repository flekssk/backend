<?php

declare(strict_types=1);

namespace App\Services\Slots\Actions;

use App\Services\Slots\Http\Requests\SlotsProvidersListRequest;
use App\Services\Slots\Repositories\SlotProviderRepository;
use FKS\Actions\Action;
use FKS\Search\Collections\EntitiesCollection;
use FKS\Search\ValueObjects\SearchConditions;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class SlotsProvidersListAction extends Action
{
    public function __construct(private readonly SlotProviderRepository $slotProviderRepository)
    {
    }

    public function handle(SearchConditions $searchConditions): EntitiesCollection|Builder|bool|Collection|null
    {
        return $this->slotProviderRepository->search($searchConditions);
    }

    public function asController(SlotsProvidersListRequest $request): Response|JsonResponse
    {
        $conditions = $request->getSearchConditions();
        return response()->json($this->handle($request->getSearchConditions())->map->only($conditions->getAvailableFields()));
    }
}
