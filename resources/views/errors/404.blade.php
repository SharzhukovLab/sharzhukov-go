<x-layouts.public :title="'404 — ' . config('app.name')">
    <x-lab::error-page
        :code="404"
        title="Lost in space"
        message="This page doesn't exist or has been moved. Check the URL, or head back to familiar territory."
        :actions="[
            ['label' => '← Back to home', 'url' => '/', 'variant' => 'primary'],
            ['label' => 'My links →', 'url' => '/my', 'variant' => 'secondary'],
        ]"
    />
</x-layouts.public>