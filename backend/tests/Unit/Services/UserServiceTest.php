<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class UserServiceTest extends TestCase
{

    private MockInterface $userRepository;
    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_all_returns_all_users(): void
    {
        $users = new Collection([new User(), new User()]);
        $this->userRepository->shouldReceive('all')->once()->andReturn($users);

        $result = $this->userService->all();

        $this->assertCount(2, $result);
    }

    public function test_find_returns_user_by_id(): void
    {
        $user = User::make(['id' => 1, 'username' => 'testuser']);
        $this->userRepository->shouldReceive('find')->with(1)->once()->andReturn($user);

        $result = $this->userService->find(1);

        $this->assertSame($user, $result);
    }

    public function test_find_returns_null_when_not_found(): void
    {
        $this->userRepository->shouldReceive('find')->with(999)->once()->andReturn(null);

        $result = $this->userService->find(999);

        $this->assertNull($result);
    }

    public function test_create_hashes_password(): void
    {
        $data = ['username' => 'newuser', 'password' => 'plaintext', 'email' => 'test@test.com', 'role_id' => 1];

        $this->userRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($input) {
                return Hash::check('plaintext', $input['password']);
            }))
            ->andReturn(new User($data));

        $result = $this->userService->create($data);

        $this->assertInstanceOf(User::class, $result);
    }

    public function test_update_hashes_password_when_provided(): void
    {
        $user = User::make(['id' => 1, 'username' => 'existing']);

        $this->userRepository->shouldReceive('update')
            ->once()
            ->with(1, Mockery::on(function ($input) {
                return Hash::check('newpassword', $input['password']);
            }))
            ->andReturn($user);

        $result = $this->userService->update(1, ['password' => 'newpassword']);

        $this->assertSame($user, $result);
    }

    public function test_update_does_not_hash_when_no_password(): void
    {
        $user = User::make(['id' => 1, 'username' => 'existing']);
        $data = ['name' => 'New Name'];

        $this->userRepository->shouldReceive('update')
            ->once()
            ->with(1, $data)
            ->andReturn($user);

        $result = $this->userService->update(1, $data);

        $this->assertSame($user, $result);
    }

    public function test_delete_returns_true_on_success(): void
    {
        $this->userRepository->shouldReceive('delete')->with(1)->once()->andReturn(true);

        $result = $this->userService->delete(1);

        $this->assertTrue($result);
    }

    public function test_delete_returns_false_when_not_found(): void
    {
        $this->userRepository->shouldReceive('delete')->with(999)->once()->andReturn(false);

        $result = $this->userService->delete(999);

        $this->assertFalse($result);
    }

    public function test_findByEmail_delegates_to_repository(): void
    {
        $user = User::make(['id' => 1, 'email' => 'test@test.com']);
        $this->userRepository->shouldReceive('findByEmail')->with('test@test.com')->once()->andReturn($user);

        $result = $this->userService->findByEmail('test@test.com');

        $this->assertSame($user, $result);
    }

    public function test_authenticate_returns_user_when_credentials_valid(): void
    {
        $user = User::make(['id' => 1, 'email' => 'test@test.com', 'password' => Hash::make('secret')]);

        $this->userRepository->shouldReceive('findByEmail')->with('test@test.com')->once()->andReturn($user);

        $result = $this->userService->authenticate('test@test.com', 'secret');

        $this->assertSame($user, $result);
    }

    public function test_authenticate_returns_null_when_user_not_found(): void
    {
        $this->userRepository->shouldReceive('findByEmail')->with('unknown@test.com')->once()->andReturn(null);

        $result = $this->userService->authenticate('unknown@test.com', 'any');

        $this->assertNull($result);
    }

    public function test_authenticate_returns_null_when_password_is_wrong(): void
    {
        $user = User::make(['id' => 1, 'email' => 'test@test.com', 'password' => Hash::make('correct')]);

        $this->userRepository->shouldReceive('findByEmail')->with('test@test.com')->once()->andReturn($user);

        $result = $this->userService->authenticate('test@test.com', 'wrong');

        $this->assertNull($result);
    }
}
