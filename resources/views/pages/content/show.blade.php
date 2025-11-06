@extends('layouts.app')

@section('title', ($page->meta_title ?? $page->title) . ' - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', $page->meta_description ?? '')
@if($page->meta_title)
    @section('meta_title', $page->meta_title)
@endif

@section('content')
    <div class="mx-auto max-w-4xl rounded-lg bg-white p-8 shadow-lg dark:bg-gray-800">
        <h1 class="mb-8 text-center text-4xl font-extrabold text-secondary">{{ $page->title }}</h1>
        <div class="prose prose-lg max-w-none dark:prose-invert">
            {!! $page->content !!}
        </div>
    </div>
@endsection

