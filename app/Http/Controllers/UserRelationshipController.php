<?php

namespace App\Http\Controllers;

use App\Enums\UserRrelationshipType;
use App\Exceptions as AE;
use App\Http\Resources\UserRelationshipCollection;
use App\Http\Resources\UserRelationshipResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use PDOException;

class UserRelationshipController extends Controller
{
    /**
     * 取得所有關係
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 15);
        $page = $request->input('page', 1);
        $type = $request->enum('type', UserRrelationshipType::class);

        if ($request->has('type') && is_null($type)) {
            throw new AE\BadRequestException(code: 10407, message: 'User relationship type is wrong.');
        }

        $children = $request->user()
            ->children()
            ->when(
                $request->has('type'),
                fn ($query) => $query->where('user_relationship.type', $type),
            )
            ->orderby('id', 'desc')
            ->paginate(perPage: $perPage, page: $page);

        return (new UserRelationshipCollection($children))->resolve();
    }

    /**
     * 新增關係
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $childId = $request->input('child_id');
        $user = $request->user();

        if ($childId === $user->id) {
            throw new AE\BadRequestException(code: 10408, message: 'Can not request self.');
        }

        $user->children()
            ->where('child_id', $childId)
            ->doesntExistOr(fn () => throw new AE\BadRequestException(code: 10401, message: 'Already requested.'));

        try {
            $child = User::findOrFail($childId);
            $user->children()->attach($child->id, ['type' => 0]);
        } catch (ModelNotFoundException $e) {
            throw new AE\ModelNotFoundException(code: 10402, message: 'Child not found.');
        } catch (PDOException $e) {
            throw new AE\QueryException(code: 10403, message: 'Attach user relationship error.', previous: $e);
        }

        return response()->json([
            'code' => 200,
            'message' => 'ok',
        ], 200);
    }

    /**
     * 查詢關係
     *
     * @param  \App\Models\User  $child
     * @return \Illuminate\Http\Response
     */
    public function show(User $child)
    {
        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'result' => new UserRelationshipResource($child),
        ], 200);
    }

    /**
     * 更新關係
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $child
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $child)
    {
        $type = $request->enum('type', UserRrelationshipType::class);

        if (is_null($type)) {
            throw new AE\BadRequestException(code: 10404, message: 'User relationship type is wrong.');
        }

        try {
            $child->relationship->type = $type;
            $child->relationship->save();
        } catch (PDOException $e) {
            throw new AE\QueryException(code: 10405, message: 'Update user relationship error.', previous: $e);
        }

        return response()->json([
            'code' => 200,
            'message' => 'ok',
        ], 200);
    }

    /**
     * 刪除關係
     *
     * @param  \App\Models\User  $child
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $child)
    {
        try {
            $child->relationship->delete();
        } catch (PDOException $e) {
            throw new AE\QueryException(code: 10406, message: 'Delete user relationship error.', previous: $e);
        }

        return response()->json([
            'code' => 200,
            'message' => 'ok',
        ], 200);
    }
}
