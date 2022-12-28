<?php

namespace Tests\Unit\Models;

use App\Models\Comment;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * 測試取得使用者評論
     *
     * @return void
     */
    public function test_select_user_comments()
    {
        $user = User::factory()
            ->has(Comment::factory()->count(3), 'comments')
            ->create();

        $this->assertEquals(3, $user->comments->count());

        $comment = $user->comments->last()->toArray();
        $this->assertArrayHasKey('id', $comment);
        $this->assertArrayHasKey('content', $comment);
        $this->assertArrayHasKey('created_at', $comment);
        $this->assertArrayHasKey('updated_at', $comment);
        $this->assertArrayHasKey('post_id', $comment);
        $this->assertArrayHasKey('post_id', $comment);
    }

    /**
     * 測試取得使用者擁有者
     *
     * @return void
     */
    public function test_select_user_owners()
    {
        $child = User::factory()
            ->hasAttached(User::factory()->count(1), ['type' => 0], 'owners')
            ->create();

        $this->assertEquals(1, $child->owners->count());

        $relationship = $child->owners->last()->relationship->toArray();
        $this->assertArrayHasKey('child_id', $relationship);
        $this->assertArrayHasKey('owner_id', $relationship);
        $this->assertArrayHasKey('id', $relationship);
        $this->assertArrayHasKey('type', $relationship);
        $this->assertArrayHasKey('created_at', $relationship);
        $this->assertArrayHasKey('updated_at', $relationship);
    }

    /**
     * 測試取得使用者下層
     *
     * @return void
     */
    public function test_select_user_children()
    {
        $owner = User::factory()
            ->hasAttached(User::factory()->count(1), ['type' => 0], 'children')
            ->create();

        $this->assertEquals(1, $owner->children->count());

        $relationship = $owner->children->last()->relationship->toArray();
        $this->assertArrayHasKey('child_id', $relationship);
        $this->assertArrayHasKey('owner_id', $relationship);
        $this->assertArrayHasKey('id', $relationship);
        $this->assertArrayHasKey('type', $relationship);
        $this->assertArrayHasKey('created_at', $relationship);
        $this->assertArrayHasKey('updated_at', $relationship);
    }
}
