<?php

namespace Tests\Unit\Http\Controllers;

use App\Exceptions\QueryException;
use App\Http\Controllers\UserRelationshipController;
use App\Models\User;
use Illuminate\Http\Request;
use Mockery;
use PDOException;
use Tests\TestCase;

class UserRelationshipControllerTest extends TestCase
{
    public function test_store_error_with_PDOException()
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionCode(10403);
        $this->expectExceptionMessage('Attach user relationship error.');

        User::factory()->create();

        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('children->where->doesntExistOr');
        $mockUser->shouldReceive('children->attach')->andThrow(new PDOException('test'));

        $mockRequest = Mockery::mock(Request::class);
        $mockRequest->shouldReceive('input')->andReturn(1);
        $mockRequest->shouldReceive('user')->andReturn($mockUser);

        (new UserRelationshipController())->store($mockRequest);
    }

    public function test_update_error_with_PDOException()
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionCode(10405);
        $this->expectExceptionMessage('Update user relationship error.');

        $mockRelationship = Mockery::mock();
        $mockRelationship->shouldReceive('save')->andThrow(new PDOException('test'));

        $mockRequest = Mockery::mock(Request::class);
        $mockRequest->shouldReceive('enum')->andReturn(1);

        $child = User::factory()
            ->hasAttached(User::factory()->count(1), ['type' => 0], 'owners')
            ->create();
        $child->relationship = $mockRelationship;

        (new UserRelationshipController())->update($mockRequest, $child);
    }

    public function test_delete_error_with_PDOException()
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionCode(10406);
        $this->expectExceptionMessage('Delete user relationship error.');

        $mockRelationship = Mockery::mock();
        $mockRelationship->shouldReceive('delete')->andThrow(new PDOException('test'));

        $child = User::factory()
            ->hasAttached(User::factory()->count(1), ['type' => 0], 'owners')
            ->create();
        $child->relationship = $mockRelationship;

        (new UserRelationshipController())->destroy($child);
    }
}
