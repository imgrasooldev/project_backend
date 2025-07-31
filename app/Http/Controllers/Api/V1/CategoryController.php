<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use App\Http\Resources\V1\SearchCategoryResource;
use App\Repositories\Interfaces\SubcategoryRepositoryInterface;

class CategoryController extends Controller
{
    protected $categoryRepo;
    protected $subcategoryRepo;

public function __construct(CategoryRepositoryInterface $categoryRepo, SubcategoryRepositoryInterface $subcategoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
        $this->subcategoryRepo = $subcategoryRepo;
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

     public function searchCategoryListDropdown(){
        $subcategories = $this->subcategoryRepo->all(); // ✅ Now using repository
        return SearchCategoryResource::collection($subcategories);
    }


}
