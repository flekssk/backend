<?php

declare(strict_types=1);

namespace App\Services\Games\Actions;

use App\Services\Games\Http\Requests\SlotsProvidersListRequest;
use App\Services\Games\Repositories\SlotProviderRepository;
use FKS\Actions\Action;
use FKS\Search\Collections\EntitiesCollection;
use FKS\Search\ValueObjects\SearchConditions;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SlotsProvidersListAction extends Action
{
    public function __construct(private readonly SlotProviderRepository $slotProviderRepository)
    {
    }

    public function handle(SearchConditions $searchConditions): EntitiesCollection|Builder|bool|Collection|null
    {
        return Cache::remember($searchConditions->hash(), 3600, fn () => $this->slotProviderRepository->search($searchConditions));
    }

    public function asController(SlotsProvidersListRequest $request): Response|JsonResponse
    {
        $conditions = $request->getSearchConditions();

        return response()->json($this->handle($request->getSearchConditions())->map->only($conditions->getAvailableFields()));
    }
}
