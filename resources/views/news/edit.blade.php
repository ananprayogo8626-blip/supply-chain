<x-app-layout>

<div class="max-w-5xl mx-auto">

<div class="bg-white shadow rounded-lg p-8">

<h1 class="text-3xl font-bold mb-8">

Edit Berita

</h1>

<form action="{{ route('news.update',$news->id) }}" method="POST">

@csrf
@method('PUT')

<div class="grid grid-cols-2 gap-6">

<div>

<label>Negara</label>

<select name="country_id" class="w-full border rounded p-2">

@foreach($countries as $country)

<option
value="{{ $country->id }}"
{{ $news->country_id==$country->id?'selected':'' }}>

{{ $country->country_name }}

</option>

@endforeach

</select>

</div>

<div>

<label>Judul</label>

<input
type="text"
name="title"
value="{{ $news->title }}"
class="w-full border rounded p-2">

</div>

<div>

<label>Source</label>

<input
type="text"
name="source"
value="{{ $news->source }}"
class="w-full border rounded p-2">

</div>

<div>

<label>Category</label>

<input
type="text"
name="category"
value="{{ $news->category }}"
class="w-full border rounded p-2">

</div>

<div>

<label>URL</label>

<input
type="url"
name="url"
value="{{ $news->url }}"
class="w-full border rounded p-2">

</div>

<div>

<label>Impact Score</label>

<input
type="number"
name="impact_score"
value="{{ $news->impact_score }}"
class="w-full border rounded p-2">

</div>

</div>

<div class="mt-6">

<label>Summary</label>

<textarea
name="summary"
rows="5"
class="w-full border rounded p-2">{{ $news->summary }}</textarea>

</div>

<div class="mt-6">

<label>Published At</label>

<input
type="datetime-local"
name="published_at"
value="{{ $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : '' }}"
class="w-full border rounded p-2">

</div>

<div class="mt-8">

<button class="bg-yellow-500 text-white px-6 py-3 rounded">

Update

</button>

<a href="{{ route('news.index') }}"
class="bg-gray-600 text-white px-6 py-3 rounded ml-2">

Kembali

</a>

</div>

</form>

</div>

</div>

</x-app-layout>