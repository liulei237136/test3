<?php

namespace Tests\Feature;

use App\Models\Commit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommitTest extends TestCase
{
    // $table->id();
    //         $table->unsignedBigInteger('author_id');
    //         $table->string('title');
    //         $table->text('description')->nullable();
    //         $table->string('file_path');
    //         $table->timestamps();
    use RefreshDatabase;

    public function test_guest_cannot_create_commit()
    {
        $response = $this->post(route('commit.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_create_commit()
    {
        //1 as a user login
        $this->actingAs($user = User::factory()->create());
        //2 when it create commit
        $content = array([
            'file_name' => 'file_name',
            'file_path' => 'audio/2022/01/07/9Vcg82LaICeNdy4wsa3Uox8lA3US5FlvCNH8Momm.mp3',
            'book_name' => 'book_name',
            'text' => 'text',
        ]);
        $response = $this->post(route('commit.store'), [
            'title' => 'title',
            'description' => 'description',
            'content' => $content,
        ]);
        //3 the database has new commit
        $this->assertCount(1, Commit::count());

        $first = Commit::first();

        $this->assertEquals($first->title, 'title');
        $this->assertEquals($first->description, 'description');
        $this->assertEquals($first->title, 'file_name');
        //4 session has no error
        //5 redirect to package/commits
    }
}
