<div>
    <form wire:submit.prevent="register">
        <div>
            <label for="name">Name</label>
            <input wire:model="name" type="text" id="name" required autofocus>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="email">Email</label>
            <input wire:model="email" type="email" id="email" required>
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="password">Password</label>
            <input wire:model="password" type="password" id="password" required>
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="password_confirmation">Confirm Password</label>
            <input wire:model="password_confirmation" type="password" id="password_confirmation" required>
        </div>

        <div>
            <button type="submit">Register</button>
        </div>
    </form>
</div>