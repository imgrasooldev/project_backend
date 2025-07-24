<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryRepo;

    public function __construct(CategoryRepositoryInterface $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function index()
    {
        return response()->json($this->categoryRepo->all(10)); // paginated
    }

    public function show($id)
    {
        return response()->json($this->categoryRepo->find($id));
    }

    public function store(Request $request)
    {
        $data = $request->only(['name', 'slug']);
        return response()->json($this->categoryRepo->create($data));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only(['name', 'slug']);
        return response()->json($this->categoryRepo->update($id, $data));
    }

    public function destroy($id)
    {
        $this->categoryRepo->delete($id);
        return response()->json(['message' => 'Category deleted successfully.']);
    }
}
