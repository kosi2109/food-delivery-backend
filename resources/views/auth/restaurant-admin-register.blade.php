<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register as Restaurant Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5" style="width: 785px; margin:0 auto">
        <h2 class="text-center mb-4">Register as Restaurant Admin</h2>

        <form method="POST" action="{{ route('register.restaurant') }}" enctype="multipart/form-data">
            @csrf
            <!-- User Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <p>Account Information</p>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <p>Restaurant Information</p>
                </div>
                <div class="card-body">
                    <!-- Restaurant Details -->
                    <div class="mb-3">
                        <label for="restaurant_name" class="form-label">Restaurant Name</label>
                        <input id="restaurant_name" class="form-control @error('restaurant_name') is-invalid @enderror" type="text" name="restaurant_name" value="{{ old('restaurant_name') }}" required>
                        @error('restaurant_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="mb-3">
                        <label for="shop_type" class="form-label">Shop Type</label>
                        <input id="shop_type" class="form-control @error('shop_type') is-invalid @enderror" type="text" name="shop_type" value="{{ old('shop_type') }}" required>
                        @error('shop_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input id="address" class="form-control @error('address') is-invalid @enderror" type="text" name="address" value="{{ old('address') }}" required>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                
                    {{-- <div class="mb-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input id="latitude" class="form-control @error('latitude') is-invalid @enderror" type="number" name="latitude" value="{{ old('latitude') }}" required>
                        @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input id="longitude" class="form-control @error('longitude') is-invalid @enderror" type="number" name="longitude" value="{{ old('longitude') }}" required>
                        @error('longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    {{-- <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <input id="rating" class="form-control @error('rating') is-invalid @enderror" type="number" name="rating" value="{{ old('rating') }}" required min="0" max="5">
                        @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" cols="30" rows="10">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        {{-- <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_popular" id="is_popular" value="1" {{ old('is_popular') == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_popular">
                             Is Popular
                            </label>
                        </div>
                    </div> --}}

                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input id="logo" class="form-control @error('logo') is-invalid @enderror" type="file" name="logo">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        
            <button type="submit" class="btn btn-primary mt-3">Register</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
