<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Story-uri seller</h2>
                <p class="mt-1 text-sm text-gray-500">Publici o singura data, iar story-ul apare si pe site, si in aplicatia mobila.</p>
            </div>

            <a href="{{ route('seller.dashboard') }}"
               class="inline-flex items-center rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Inapoi in dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                    <div class="text-sm font-semibold text-red-700">Exista erori:</div>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="max-w-3xl">
                    <div class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">Story nou</div>
                    <h3 class="mt-2 text-2xl font-semibold text-gray-900">Tine legatura cu clientii prin foto si video</h3>
                    <p class="mt-2 text-sm leading-7 text-gray-600">
                        Story-ul ramane activ 24 de ore si se afiseaza public in marketplace. Cei care te urmaresc il vor vedea cu prioritate, dar il pot vedea si clientii care nu te urmaresc inca.
                    </p>
                </div>

                <form method="POST" action="{{ route('seller.stories.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-5 lg:grid-cols-[1.1fr_0.9fr]">
                    @csrf

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                        <label class="block text-sm font-medium text-gray-700">Poza sau video</label>
                        <input type="file" name="media" accept="image/*,video/mp4,video/webm,video/quicktime" class="mt-3 block w-full text-sm text-gray-700" required>
                        <p class="mt-3 text-xs leading-6 text-gray-500">
                            Acceptam imagini si video. Imaginile noi se optimizeaza automat, iar video-ul se pastreaza pentru story asa cum a fost incarcat.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                        <label class="block text-sm font-medium text-gray-700">Text optional</label>
                        <textarea name="caption" rows="5" maxlength="280" placeholder="Ex: A intrat colectia noua, livrare rapida, reducere azi..." class="mt-3 w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm"></textarea>
                        <div class="mt-5">
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-black">
                                Publica story
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">Active acum</div>
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">{{ $activeStories->count() }} story-uri active</h3>
                    </div>
                </div>

                @if($activeStories->isEmpty())
                    <div class="mt-6 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-10 text-center text-sm text-gray-500">
                        Nu ai story-uri active acum.
                    </div>
                @else
                    <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($activeStories as $story)
                            <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                                <div class="aspect-[9/16] bg-black">
                                    @if($story->media_type === 'video')
                                        <video src="{{ \App\Support\MediaUrl::public($story->media_path) }}" class="h-full w-full object-cover" controls muted playsinline></video>
                                    @else
                                        <img src="{{ \App\Support\MediaUrl::public($story->media_path) }}" alt="Story" class="h-full w-full object-cover">
                                    @endif
                                </div>
                                <div class="p-4">
                                    <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Expira</div>
                                    <div class="mt-1 text-sm font-medium text-gray-900">{{ $story->expires_at?->format('d.m.Y H:i') }}</div>
                                    @if($story->caption)
                                        <div class="mt-3 text-sm leading-6 text-gray-600">{{ $story->caption }}</div>
                                    @endif
                                    <form method="POST" action="{{ route('seller.stories.destroy', $story) }}" class="mt-4">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center rounded-xl border border-red-300 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">
                                            Sterge story
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            @if($recentExpiredStories->isNotEmpty())
                <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-400">Istoric scurt</div>
                    <h3 class="mt-2 text-xl font-semibold text-gray-900">Ultimele story-uri expirate</h3>

                    <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4">
                        @foreach($recentExpiredStories as $story)
                            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-gray-50">
                                <div class="aspect-[3/4] bg-black">
                                    @if($story->media_type === 'video')
                                        <video src="{{ \App\Support\MediaUrl::public($story->media_path) }}" class="h-full w-full object-cover" muted playsinline></video>
                                    @else
                                        <img src="{{ \App\Support\MediaUrl::public($story->media_path) }}" alt="Story expirat" class="h-full w-full object-cover opacity-80">
                                    @endif
                                </div>
                                <div class="px-3 py-3 text-xs text-gray-500">
                                    Expirat la {{ $story->expires_at?->format('d.m H:i') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
