<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_read_threads_but_must_log_in_to_post(): void
    {
        $user = User::factory()->create();
        $thread = Thread::create([
            'title' => 'Games',
            'description' => 'Praat over games',
            'user_id' => $user->id,
        ]);
        $topic = Topic::create([
            'title' => 'Favoriete game',
            'body' => 'Welke game spelen jullie?',
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);

        $this->get(route('threads.show', $thread))
            ->assertOk()
            ->assertSee('Favoriete game');

        $this->post(route('replies.store', $topic), ['body' => 'Ik speel Minecraft.'])
            ->assertRedirect(route('login'));
    }

    public function test_home_page_shows_categories_topic_counts_and_single_line_descriptions(): void
    {
        $user = User::factory()->create();
        $thread = Thread::create([
            'title' => 'Games',
            'description' => 'Een uitgebreide beschrijving van deze categorie',
            'user_id' => $user->id,
        ]);
        Topic::create([
            'title' => 'Favoriete game',
            'body' => 'Welke game spelen jullie?',
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);

        $this->get(route('threads.index'))
            ->assertOk()
            ->assertSee('Games')
            ->assertSee('1 onderwerp')
            ->assertSee('class="gh-category-description"', false)
            ->assertSee('Een uitgebreide beschrijving van deze categorie');
    }

    public function test_users_can_create_topics_and_replies(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'username' => 'gamer_one',
        ]);
        $thread = Thread::create([
            'title' => 'Games',
            'description' => 'Praat over games',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('topics.store', $thread), [
                'title' => 'Favoriete game',
                'body' => 'Welke game spelen jullie?',
            ])
            ->assertRedirect(route('threads.show', $thread));

        $topic = Topic::firstOrFail();
        $this->post(route('replies.store', $topic), ['body' => 'Ik speel Minecraft.'])
            ->assertRedirect(route('threads.show', $thread));

        $this->assertDatabaseHas('replies', [
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'body' => 'Ik speel Minecraft.',
        ]);

        $this->get(route('threads.show', $thread))
            ->assertSee('Gestart door gamer_one')
            ->assertSee('gamer_one')
            ->assertDontSee('Test User');
    }

    public function test_only_topic_and_reply_owners_or_admins_can_edit_them(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $thread = Thread::create([
            'title' => 'Games',
            'description' => 'Praat over games',
            'user_id' => $owner->id,
        ]);
        $topic = Topic::create([
            'title' => 'Oude titel',
            'body' => 'Oud bericht',
            'thread_id' => $thread->id,
            'user_id' => $owner->id,
        ]);
        $reply = Reply::create([
            'body' => 'Oude reactie',
            'topic_id' => $topic->id,
            'user_id' => $owner->id,
        ]);

        $this->actingAs($otherUser)
            ->patch(route('topics.update', [$thread, $topic]), [
                'title' => 'Nieuwe titel',
                'body' => 'Nieuw bericht',
            ])
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->patch(route('replies.update', [$topic, $reply]), ['body' => 'Nieuwe reactie'])
            ->assertForbidden();

        $this->actingAs($owner)
            ->patch(route('topics.update', [$thread, $topic]), [
                'title' => 'Nieuwe titel',
                'body' => 'Nieuw bericht',
            ])
            ->assertRedirect(route('threads.show', $thread));

        $this->assertDatabaseHas('topics', ['id' => $topic->id, 'title' => 'Nieuwe titel']);
    }

    public function test_only_admins_can_create_threads_and_manage_users(): void
    {
        $regularUser = User::factory()->create();
        $this->actingAs($regularUser)
            ->post(route('threads.store'), ['title' => 'Games', 'description' => 'Game forum'])
            ->assertForbidden();
        $this->get(route('users.index'))->assertForbidden();

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin)
            ->post(route('threads.store'), ['title' => 'Games', 'description' => 'Game forum'])
            ->assertRedirect(route('threads.index'));

        $this->get(route('users.index'))
            ->assertOk()
            ->assertDontSee('password')
            ->assertSee(route('profile.edit'));

        $this->get(route('profile.edit'))
            ->assertOk()
            ->assertSee(route('users.index'));
    }

    public function test_admin_can_edit_user_details_and_role(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('users.show', $user))
            ->assertOk()
            ->assertDontSee('name="password"');

        $this->patch(route('users.update', $user), [
            'name' => 'Updated User',
            'username' => 'updateduser',
            'email' => $user->email,
            'is_admin' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User',
            'username' => 'updateduser',
            'is_admin' => true,
        ]);
    }

    public function test_only_admins_can_delete_forum_content(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $thread = Thread::create([
            'title' => 'Games',
            'description' => 'Praat over games',
            'user_id' => $owner->id,
        ]);
        $topic = Topic::create([
            'title' => 'Favoriete game',
            'body' => 'Welke game spelen jullie?',
            'thread_id' => $thread->id,
            'user_id' => $owner->id,
        ]);
        $reply = Reply::create([
            'body' => 'Ik speel Minecraft.',
            'topic_id' => $topic->id,
            'user_id' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->get(route('threads.show', $thread))
            ->assertDontSee('Onderwerp verwijderen')
            ->assertDontSee('Reactie verwijderen');

        $this->actingAs($admin)
            ->get(route('threads.show', $thread))
            ->assertSee('Onderwerp verwijderen')
            ->assertSee('Reactie verwijderen');

        $this->actingAs($owner)
            ->delete(route('threads.destroy', $thread))
            ->assertForbidden();

        $this->delete(route('topics.destroy', [$thread, $topic]))->assertForbidden();
        $this->delete(route('replies.destroy', [$topic, $reply]))->assertForbidden();
        $this->assertDatabaseHas('topics', ['id' => $topic->id]);
        $this->assertDatabaseHas('replies', ['id' => $reply->id]);

        $this->actingAs($admin)
            ->delete(route('replies.destroy', [$topic, $reply]))
            ->assertRedirect(route('threads.show', $thread));
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->delete(route('topics.destroy', [$thread, $topic]))
            ->assertRedirect(route('threads.show', $thread));
        $this->assertDatabaseMissing('topics', ['id' => $topic->id]);

        $this->delete(route('threads.destroy', $thread))
            ->assertRedirect(route('threads.index'));
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
    }

    public function test_deleting_a_topic_also_deletes_its_replies(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $thread = Thread::create([
            'title' => 'Games',
            'description' => 'Praat over games',
            'user_id' => $admin->id,
        ]);
        $topic = Topic::create([
            'title' => 'Favoriete game',
            'body' => 'Welke game spelen jullie?',
            'thread_id' => $thread->id,
            'user_id' => $admin->id,
        ]);
        $reply = Reply::create([
            'body' => 'Ik speel Minecraft.',
            'topic_id' => $topic->id,
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('topics.destroy', [$thread, $topic]))
            ->assertRedirect(route('threads.show', $thread));

        $this->assertDatabaseMissing('topics', ['id' => $topic->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }

    public function test_deleting_a_category_also_deletes_its_topics_and_replies(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $thread = Thread::create([
            'title' => 'Games',
            'description' => 'Praat over games',
            'user_id' => $admin->id,
        ]);
        $topic = Topic::create([
            'title' => 'Favoriete game',
            'body' => 'Welke game spelen jullie?',
            'thread_id' => $thread->id,
            'user_id' => $admin->id,
        ]);
        $reply = Reply::create([
            'body' => 'Ik speel Minecraft.',
            'topic_id' => $topic->id,
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('threads.destroy', $thread))
            ->assertRedirect(route('threads.index'));

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('topics', ['id' => $topic->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }
}
