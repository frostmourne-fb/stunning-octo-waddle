<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\MemberController;
use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    private $membersController;

    public function testRendersMembersList()
    {
        /** @var Collection $members */
        $members = Member::factory()
            ->count(2)
            ->create();

        $response = $this->get( '/');
        $response->assertOk();
        $response->assertViewIs('index');

        $response->assertViewHas('members', function (Collection $membersInView) use ($members) {
            return $members->diff($membersInView)->isEmpty();
        });
    }

    public function testCreatesMembersSuccessfully()
    {
        $name = 'Bohdan';
        $email = 'test@mail.com';
        $request = StoreMemberRequest::create('/members', 'POST', compact(['name', 'email']));
        /** @var Member $response */
        $response = $this->membersController->store($request);

        $this->assertNotNull($response);
        $this->assertDatabaseHas('members',[
            'email' => $email,
            'name' => $name
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->membersController = new MemberController();
    }
}
