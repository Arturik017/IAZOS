<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Mesaje</h2>
                <p class="text-sm text-gray-500">Conversatii intre client-seller si seller-admin.</p>
            </div>

            @if(auth()->user()->role === 'seller')
                <form method="POST" action="{{ route('messages.start_admin') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                        Scrie adminului
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    @php
        $messageStoryGroupsPayload = collect($messageStoryGroups ?? [])->values();
    @endphp

    <script>
        window.messageStoryConfig = {
            groups: @json($messageStoryGroupsPayload),
            isAuthenticated: @json(auth()->check()),
            loginUrl: @json(route('login')),
            csrfToken: @json(csrf_token()),
            storyMessageUrlTemplate: @json(route('stories.message', ['story' => '__STORY__'])),
            storyLikeUrlTemplate: @json(route('stories.like', ['story' => '__STORY__'])),
            sellerShowUrlTemplate: @json(route('seller.public.show', ['user' => '__SELLER__'])),
        };

        window.openMessageStoryBySeller = function (sellerId, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            if (window.messageStoryRuntime && typeof window.messageStoryRuntime.openGroupBySeller === 'function') {
                window.messageStoryRuntime.openGroupBySeller(sellerId);
            }
        };

        window.messageStoryViewer = function (config) {
            return {
                groups: config.groups || [],
                timeline: [],
                isOpen: false,
                activeIndex: 0,
                imageTimer: null,
                imageDuration: 5500,
                imageStartedAt: null,
                currentProgress: 0,
                videoError: false,
                videoProgress: 0,
                seenStories: {},
                storyMessageText: '',
                storyMessageBusy: false,
                storyLikeBusy: false,
                toastMessage: '',

                init() {
                    window.messageStoryRuntime = this;
                    this.loadSeen();
                    this.buildTimeline();
                },

                buildTimeline() {
                    this.timeline = [];

                    this.groups.forEach((group) => {
                        (group.stories || []).forEach((story) => {
                            this.timeline.push({
                                ...story,
                                seller_id: group.seller_id,
                                seller_name: group.seller_name,
                                seller_avatar: group.seller_avatar,
                                seller_url: group.seller_url,
                                seller_initial: (group.seller_name || 'S').trim().charAt(0).toUpperCase() || 'S',
                                group_story_ids: (group.stories || []).map(item => item.id),
                            });
                        });
                    });
                },

                loadSeen() {
                    try {
                        this.seenStories = JSON.parse(window.localStorage.getItem('iazos_seen_stories') || '{}') || {};
                    } catch (e) {
                        this.seenStories = {};
                    }
                },

                saveSeen() {
                    window.localStorage.setItem('iazos_seen_stories', JSON.stringify(this.seenStories));
                },

                markSeen(storyId) {
                    if (!storyId) return;
                    this.seenStories[String(storyId)] = true;
                    this.saveSeen();
                },

                currentSellerUrl() {
                    const item = this.currentItem();
                    if (!item) return '#';
                    return item.seller_url || config.sellerShowUrlTemplate.replace('__SELLER__', item.seller_id);
                },

                ensureAuth() {
                    if (config.isAuthenticated) {
                        return true;
                    }

                    window.location.href = config.loginUrl;
                    return false;
                },

                storyMessageUrl(storyId) {
                    return config.storyMessageUrlTemplate.replace('__STORY__', storyId);
                },

                storyLikeUrl(storyId) {
                    return config.storyLikeUrlTemplate.replace('__STORY__', storyId);
                },

                setToast(message) {
                    this.toastMessage = message;
                    setTimeout(() => {
                        if (this.toastMessage === message) {
                            this.toastMessage = '';
                        }
                    }, 2200);
                },

                openGroupBySeller(sellerId) {
                    const index = this.timeline.findIndex(item => Number(item.seller_id) === Number(sellerId));
                    if (index === -1) return;
                    this.activeIndex = index;
                    this.isOpen = true;
                    this.storyMessageText = '';
                    this.$nextTick(() => this.playCurrent());
                },

                closeStory() {
                    this.isOpen = false;
                    this.stopImageTimer();
                    this.storyMessageText = '';
                    if (this.$refs.storyVideo) {
                        this.$refs.storyVideo.pause();
                    }
                },

                currentItem() {
                    return this.timeline[this.activeIndex] || null;
                },

                prev() {
                    if (!this.timeline.length) return;
                    this.activeIndex = this.activeIndex === 0 ? this.timeline.length - 1 : this.activeIndex - 1;
                    this.playCurrent();
                },

                next() {
                    if (!this.timeline.length) return;
                    this.activeIndex = this.activeIndex === this.timeline.length - 1 ? 0 : this.activeIndex + 1;
                    this.playCurrent();
                },

                playCurrent() {
                    this.stopImageTimer();
                    this.currentProgress = 0;
                    this.videoProgress = 0;
                    this.videoError = false;
                    this.storyMessageText = '';

                    this.$nextTick(() => {
                        const item = this.currentItem();
                        if (!item) return;
                        this.markSeen(item.id);

                        if (item.media_type === 'video' && this.$refs.storyVideo) {
                            this.$refs.storyVideo.currentTime = 0;
                            this.$refs.storyVideo.muted = false;
                            this.$refs.storyVideo.volume = 1;
                            this.$refs.storyVideo.play().catch(() => {});
                            return;
                        }

                        this.startImageTimer();
                    });
                },

                startImageTimer() {
                    this.imageStartedAt = Date.now();
                    this.currentProgress = 0;

                    this.imageTimer = setInterval(() => {
                        const elapsed = Date.now() - this.imageStartedAt;
                        this.currentProgress = Math.min(100, (elapsed / this.imageDuration) * 100);

                        if (elapsed >= this.imageDuration) {
                            this.next();
                        }
                    }, 80);
                },

                stopImageTimer() {
                    if (this.imageTimer) {
                        clearInterval(this.imageTimer);
                    }
                    this.imageTimer = null;
                },

                updateVideoProgress() {
                    if (!this.$refs.storyVideo) return;
                    const duration = this.$refs.storyVideo.duration || 0;
                    const currentTime = this.$refs.storyVideo.currentTime || 0;
                    if (duration > 0) {
                        this.videoProgress = Math.min(100, (currentTime / duration) * 100);
                        this.currentProgress = Math.min(100, (currentTime / duration) * 100);
                    }
                },

                progressStyle(index) {
                    if (index < this.activeIndex) return 'width: 100%';
                    if (index > this.activeIndex) return 'width: 0%';
                    return `width: ${this.currentProgress}%`;
                },

                async sendStoryMessage() {
                    if (!this.ensureAuth()) return;

                    const item = this.currentItem();
                    const body = (this.storyMessageText || '').trim();
                    if (!item || !body || this.storyMessageBusy) return;

                    this.storyMessageBusy = true;

                    try {
                        const response = await fetch(this.storyMessageUrl(item.id), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': config.csrfToken,
                            },
                            body: JSON.stringify({ body }),
                        });

                        const payload = await response.json();

                        if (!response.ok || !payload.ok) {
                            throw new Error(payload.message || 'Mesajul nu a putut fi trimis.');
                        }

                        this.storyMessageText = '';
                        window.location.href = payload.conversation_url;
                    } catch (error) {
                        this.setToast(error.message || 'Mesajul nu a putut fi trimis.');
                    } finally {
                        this.storyMessageBusy = false;
                    }
                },

                async toggleStoryLike() {
                    if (!this.ensureAuth()) return;

                    const item = this.currentItem();
                    if (!item || this.storyLikeBusy) return;

                    this.storyLikeBusy = true;

                    try {
                        const method = item.is_liked ? 'DELETE' : 'POST';
                        const response = await fetch(this.storyLikeUrl(item.id), {
                            method,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': config.csrfToken,
                            },
                        });

                        const payload = await response.json();

                        if (!response.ok || !payload.ok) {
                            throw new Error(payload.message || 'Nu am putut actualiza aprecierea.');
                        }

                        this.timeline = this.timeline.map((story) => {
                            if (Number(story.id) !== Number(item.id)) {
                                return story;
                            }

                            return {
                                ...story,
                                is_liked: !!payload.liked,
                                likes_count: Number(payload.likes_count || 0),
                            };
                        });
                    } catch (error) {
                        this.setToast(error.message || 'Nu am putut actualiza aprecierea.');
                    } finally {
                        this.storyLikeBusy = false;
                    }
                },
            };
        };
    </script>

    <div class="py-10" x-data="window.messageStoryViewer(window.messageStoryConfig)" x-init="init()">
        <div class="mx-auto max-w-7xl space-y-6 px-4">
            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-[360px,1fr]">
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <div class="text-lg font-semibold text-gray-900">Inbox</div>
                        <div class="text-sm text-gray-500">{{ $conversations->count() }} conversatii</div>
                    </div>

                    <div class="max-h-[70vh] overflow-y-auto">
                        @forelse($conversations as $conversation)
                            @php
                                $other = $conversation->otherParticipantFor(auth()->user());
                                $otherName = $other?->sellerProfile?->shop_name ?? $other?->name ?? 'Conversatie';
                                $avatar = $other?->sellerProfile?->avatar_path ? \App\Support\MediaUrl::public($other->sellerProfile->avatar_path) : null;
                                $initial = strtoupper(mb_substr($otherName, 0, 1));
                                $isActive = $activeConversation && (int) $activeConversation->id === (int) $conversation->id;
                                $otherStoryIds = collect($conversation->other_active_story_ids ?? [])->values()->all();
                                $otherHasStories = (bool) ($conversation->other_has_active_stories ?? false);
                            @endphp

                            <div class="flex gap-3 border-b border-gray-100 px-5 py-4 transition hover:bg-gray-50 {{ $isActive ? 'bg-gray-50' : '' }}">
                                @if($otherHasStories && $other && ($other->role ?? null) === 'seller')
                                    <button type="button" onclick="window.openMessageStoryBySeller({{ $other->id }}, event)" class="story-ring h-12 w-12 shrink-0 rounded-full p-[2px]" data-story-ids='@json($otherStoryIds)'>
                                        <div class="h-full w-full overflow-hidden rounded-full border-2 border-white bg-gray-50">
                                            @if($avatar)
                                                <img src="{{ $avatar }}" alt="{{ $otherName }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-sm font-bold text-gray-500">
                                                    {{ $initial }}
                                                </div>
                                            @endif
                                        </div>
                                    </button>
                                @else
                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-full border border-gray-200 bg-gray-50">
                                        @if($avatar)
                                            <img src="{{ $avatar }}" alt="{{ $otherName }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-sm font-bold text-gray-500">
                                                {{ $initial }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <a href="{{ route('messages.show', $conversation) }}" class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="truncate text-sm font-semibold text-gray-900">{{ $otherName }}</div>
                                        @if($conversation->unread_messages_count > 0)
                                            <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-gray-900 px-1.5 py-0.5 text-[11px] font-semibold text-white">
                                                {{ $conversation->unread_messages_count }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($conversation->product)
                                        <div class="mt-2 inline-flex max-w-full items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs text-blue-700">
                                            <span class="font-medium">Context</span>
                                            <span class="truncate">{{ $conversation->product->name }}</span>
                                        </div>
                                    @endif

                                    <div class="mt-2 truncate text-sm text-gray-500">
                                        {{ $conversation->latestMessage?->body ?? 'Fara mesaje inca.' }}
                                    </div>

                                    <div class="mt-2 text-xs text-gray-400">
                                        {{ $conversation->last_message_at?->format('d.m.Y H:i') ?? $conversation->created_at?->format('d.m.Y H:i') }}
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="px-5 py-10 text-center text-sm text-gray-500">
                                Nu ai conversatii inca.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow" x-data="{ replying: null }" x-on:message-reply.window="replying = $event.detail">
                    @if($activeConversation)
                        @php
                            $other = $activeConversation->otherParticipantFor(auth()->user());
                            $otherName = $other?->sellerProfile?->shop_name ?? $other?->name ?? 'Conversatie';
                            $avatar = $other?->sellerProfile?->avatar_path ? \App\Support\MediaUrl::public($other->sellerProfile->avatar_path) : null;
                            $initial = strtoupper(mb_substr($otherName, 0, 1));
                            $contextProduct = $activeConversation->product;
                            $contextProductImage = $contextProduct?->image ? \App\Support\MediaUrl::public($contextProduct->image) : null;
                            $headerStoryIds = collect($activeConversation->other_active_story_ids ?? [])->values()->all();
                            $headerHasStories = (bool) ($activeConversation->other_has_active_stories ?? false);
                        @endphp

                        <div class="border-b border-gray-100 px-6 py-5">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    @if($headerHasStories && $other && ($other->role ?? null) === 'seller')
                                        <button type="button" onclick="window.openMessageStoryBySeller({{ $other->id }}, event)" class="story-ring h-12 w-12 rounded-full p-[2px]" data-story-ids='@json($headerStoryIds)'>
                                            <div class="h-full w-full overflow-hidden rounded-full border-2 border-white bg-gray-50">
                                                @if($avatar)
                                                    <img src="{{ $avatar }}" alt="{{ $otherName }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-sm font-bold text-gray-500">
                                                        {{ $initial }}
                                                    </div>
                                                @endif
                                            </div>
                                        </button>
                                    @else
                                        <div class="h-12 w-12 overflow-hidden rounded-full border border-gray-200 bg-gray-50">
                                            @if($avatar)
                                                <img src="{{ $avatar }}" alt="{{ $otherName }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-sm font-bold text-gray-500">
                                                    {{ $initial }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div>
                                        <div class="text-base font-semibold text-gray-900">{{ $otherName }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $activeConversation->type === 'seller_admin' ? 'Conversatie seller-admin' : 'Conversatie seller-client' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($contextProduct)
                            <div class="border-b border-gray-100 bg-gray-50/80 px-6 py-4">
                                <div class="flex items-center gap-3 rounded-2xl border border-gray-200 bg-white p-3">
                                    <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                                        @if($contextProductImage)
                                            <img src="{{ $contextProductImage }}" alt="{{ $contextProduct->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-gray-400">
                                                Fara
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-400">
                                            Context produs
                                        </div>
                                        <div class="truncate text-sm font-semibold text-gray-900">
                                            {{ $contextProduct->name }}
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            Conversatia continua in acelasi chat, doar cu acest context actualizat.
                                        </div>
                                    </div>

                                    <a
                                        href="{{ route('product.show', $contextProduct) }}"
                                        class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-900 hover:bg-gray-50"
                                    >
                                        Vezi produsul
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="max-h-[52vh] space-y-4 overflow-y-auto bg-gray-50 px-6 py-6">
                            @foreach($messages as $message)
                                @php
                                    $mine = (int) $message->sender_id === (int) auth()->id();
                                    $senderName = $message->sender?->sellerProfile?->shop_name ?? $message->sender?->name ?? 'User';
                                    $messageImage = \App\Support\MessageState::supportsMessageMedia() && $message->image_path
                                        ? \App\Support\MediaUrl::public($message->image_path)
                                        : null;
                                    $replyTo = \App\Support\MessageState::supportsThreadingAndStoryContext() ? $message->replyTo : null;
                                    $storyContext = \App\Support\MessageState::supportsThreadingAndStoryContext() ? $message->story : null;
                                    $storyContextThumb = $storyContext ? \App\Support\MediaUrl::public($storyContext->thumbnail_path ?: $storyContext->media_path) : null;
                                @endphp

                                <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                                    @if($mine)
                                        <div
                                            class="relative inline-flex w-fit max-w-xl shrink-0 flex-col"
                                            x-data="{
                                                menuOpen: false,
                                                editing: false,
                                                pressTimer: null,
                                                openMenu() { this.menuOpen = true },
                                                closeMenu() { this.menuOpen = false },
                                                startPress() { this.pressTimer = setTimeout(() => { this.menuOpen = true }, 380) },
                                                cancelPress() { if (this.pressTimer) { clearTimeout(this.pressTimer); this.pressTimer = null; } },
                                                startEditing() { this.editing = true; this.closeMenu(); },
                                                stopEditing() { this.editing = false; this.closeMenu(); }
                                            }"
                                            x-on:click.outside="closeMenu()"
                                            x-on:contextmenu.prevent="openMenu()"
                                            x-on:touchstart="startPress()"
                                            x-on:touchend="cancelPress()"
                                            x-on:touchmove="cancelPress()"
                                        >
                                            <template x-if="!editing">
                                                <div class="rounded-2xl bg-gray-900 px-4 py-3 text-white shadow-sm">
                                                    <div class="text-xs text-white/70">{{ $senderName }}</div>

                                                    @if($storyContext)
                                                        <div class="mt-3 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-2.5">
                                                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-white/10">
                                                                @if($storyContextThumb)
                                                                    <img src="{{ $storyContextThumb }}" alt="Story context" class="h-full w-full object-cover">
                                                                @else
                                                                    <div class="flex h-full w-full items-center justify-center text-[10px] font-semibold text-white/60">
                                                                        Story
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="min-w-0">
                                                                <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-white/50">Story</div>
                                                                <div class="truncate text-xs text-white/80">{{ $storyContext->caption ?: 'Mesaj trimis din story-ul sellerului.' }}</div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($replyTo)
                                                        <div class="mt-3 rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
                                                            <div class="text-[11px] font-semibold text-white/60">{{ $replyTo->sender?->sellerProfile?->shop_name ?? $replyTo->sender?->name ?? 'Mesaj' }}</div>
                                                            <div class="mt-1 line-clamp-2 text-xs text-white/80">
                                                                {{ $replyTo->body ?: ($replyTo->image_path ? 'Imagine atasata' : 'Mesaj fara text') }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($message->body)
                                                        <div class="mt-1 whitespace-pre-wrap text-sm leading-6">{{ $message->body }}</div>
                                                    @endif

                                                    @if($messageImage)
                                                        <a href="{{ $messageImage }}" target="_blank" rel="noopener noreferrer" class="mt-3 block">
                                                            <img
                                                                src="{{ $messageImage }}"
                                                                alt="Imagine mesaj"
                                                                class="max-h-72 rounded-2xl border border-white/10 object-cover"
                                                            >
                                                        </a>
                                                    @endif

                                                    <div class="mt-2 text-[11px] text-white/60">
                                                        {{ $message->created_at?->format('d.m.Y H:i') }}
                                                        @if(\App\Support\MessageState::supportsMessageMedia() && $message->edited_at)
                                                            · editat
                                                        @endif
                                                    </div>

                                                    <button
                                                        type="button"
                                                        class="mt-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/60 transition hover:text-white"
                                                        x-on:click="$dispatch('message-reply', {
                                                            id: {{ $message->id }},
                                                            sender: @js($senderName),
                                                            body: @js($message->body ?: ($message->image_path ? 'Imagine atasata' : 'Mesaj fara text'))
                                                        })"
                                                    >
                                                        Reply
                                                    </button>
                                                </div>
                                            </template>

                                            <template x-if="editing">
                                                <form
                                                    method="POST"
                                                    action="{{ route('messages.update', [$activeConversation, $message]) }}"
                                                    enctype="multipart/form-data"
                                                    class="space-y-3 rounded-2xl bg-gray-900 px-4 py-3 text-white shadow-sm"
                                                >
                                                    @csrf
                                                    @method('PUT')

                                                    <textarea
                                                        name="body"
                                                        rows="3"
                                                        class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm text-gray-900"
                                                        placeholder="Actualizeaza mesajul..."
                                                    >{{ $message->body }}</textarea>

                                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                                        <div class="flex items-center gap-3">
                                                            <label class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition hover:bg-white/20">
                                                                <input type="file" name="image" accept="image/*" class="hidden">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-9-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                                                                </svg>
                                                            </label>

                                                            @if($messageImage)
                                                                <label class="inline-flex items-center gap-2 text-xs text-white/80">
                                                                    <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300">
                                                                    Sterge imaginea
                                                                </label>
                                                            @endif
                                                        </div>

                                                        <div class="flex items-center gap-2">
                                                            <button
                                                                type="button"
                                                                x-on:click="stopEditing()"
                                                                class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold text-white hover:bg-white/20"
                                                            >
                                                                Anuleaza
                                                            </button>
                                                            <button type="submit" class="rounded-xl bg-white px-4 py-2 text-xs font-semibold text-gray-900 hover:bg-gray-100">
                                                                Salveaza
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </template>

                                            <div
                                                x-cloak
                                                x-show="menuOpen && !editing"
                                                x-transition:enter="transition ease-out duration-180"
                                                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                x-transition:leave="transition ease-in duration-120"
                                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                class="absolute right-2 top-2 z-20 min-w-44 overflow-hidden rounded-2xl border border-gray-200/80 bg-white/95 p-1 text-sm text-gray-900 shadow-[0_18px_50px_rgba(0,0,0,0.18)] backdrop-blur sm:right-full sm:top-1/2 sm:mr-2 sm:-translate-y-1/2"
                                            >
                                                <button
                                                    type="button"
                                                    x-on:click="startEditing()"
                                                    class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left font-medium transition hover:bg-gray-50"
                                                >
                                                    <span>Editeaza</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L8.25 19.463 4 20l.537-4.25 12.325-11.263Z" />
                                                    </svg>
                                                </button>

                                                <form method="POST" action="{{ route('messages.destroy', [$activeConversation, $message]) }}" onsubmit="return confirm('Stergi definitiv acest mesaj?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left font-medium text-red-600 transition hover:bg-red-50">
                                                        <span>Sterge</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3h6m-7 4h8m-7 4v6m6-6v6M6 7l1 12a2 2 0 0 0 1.993 1.834L9 21h6a2 2 0 0 0 1.995-1.85L18 7" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <div class="relative inline-block w-fit max-w-xl shrink-0 rounded-2xl border border-gray-100 bg-white px-4 py-3 text-gray-900 shadow-sm">
                                            <div class="text-xs text-gray-400">{{ $senderName }}</div>

                                            @if($storyContext)
                                                <div class="mt-3 flex items-center gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-3 py-2.5">
                                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-gray-100">
                                                        @if($storyContextThumb)
                                                            <img src="{{ $storyContextThumb }}" alt="Story context" class="h-full w-full object-cover">
                                                        @else
                                                            <div class="flex h-full w-full items-center justify-center text-[10px] font-semibold text-gray-400">
                                                                Story
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-gray-400">Story</div>
                                                        <div class="truncate text-xs text-gray-600">{{ $storyContext->caption ?: 'Mesaj trimis din story-ul sellerului.' }}</div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($replyTo)
                                                <div class="mt-3 rounded-2xl border border-gray-200 bg-gray-50 px-3 py-2">
                                                    <div class="text-[11px] font-semibold text-gray-500">{{ $replyTo->sender?->sellerProfile?->shop_name ?? $replyTo->sender?->name ?? 'Mesaj' }}</div>
                                                    <div class="mt-1 line-clamp-2 text-xs text-gray-600">
                                                        {{ $replyTo->body ?: ($replyTo->image_path ? 'Imagine atasata' : 'Mesaj fara text') }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($message->body)
                                                <div class="mt-1 whitespace-pre-wrap text-sm leading-6">{{ $message->body }}</div>
                                            @endif

                                            @if($messageImage)
                                                <a href="{{ $messageImage }}" target="_blank" rel="noopener noreferrer" class="mt-3 block">
                                                    <img
                                                        src="{{ $messageImage }}"
                                                        alt="Imagine mesaj"
                                                        class="max-h-72 rounded-2xl border border-gray-200 object-cover"
                                                    >
                                                </a>
                                            @endif

                                            <div class="mt-2 text-[11px] text-gray-400">
                                                {{ $message->created_at?->format('d.m.Y H:i') }}
                                                @if(\App\Support\MessageState::supportsMessageMedia() && $message->edited_at)
                                                    · editat
                                                @endif
                                            </div>

                                            <button
                                                type="button"
                                                class="mt-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-gray-400 transition hover:text-gray-700"
                                                x-on:click="$dispatch('message-reply', {
                                                    id: {{ $message->id }},
                                                    sender: @js($senderName),
                                                    body: @js($message->body ?: ($message->image_path ? 'Imagine atasata' : 'Mesaj fara text'))
                                                })"
                                            >
                                                Reply
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <form method="POST" action="{{ route('messages.store', $activeConversation) }}" enctype="multipart/form-data" class="border-t border-gray-100 p-5" x-data="{ imageName: '' }">
                            @csrf
                            <input type="hidden" name="reply_to_message_id" :value="replying ? replying.id : ''">
                            <div class="space-y-3">
                                <div
                                    x-cloak
                                    x-show="replying"
                                    class="flex items-start justify-between gap-3 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3"
                                >
                                    <div class="min-w-0">
                                        <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-blue-500">Reply</div>
                                        <div class="text-sm font-semibold text-gray-900" x-text="replying ? replying.sender : ''"></div>
                                        <div class="truncate text-sm text-gray-600" x-text="replying ? replying.body : ''"></div>
                                    </div>

                                    <button type="button" class="shrink-0 rounded-full bg-white px-2 py-1 text-xs font-semibold text-gray-500 hover:bg-gray-100" x-on:click="replying = null">
                                        Inchide
                                    </button>
                                </div>

                                <div class="flex items-end gap-3">
                                <div class="min-w-0 flex-1">
                                    <textarea
                                        name="body"
                                        rows="1"
                                        class="min-h-[48px] w-full rounded-2xl border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                                        placeholder="Scrie mesajul..."
                                    >{{ old('body') }}</textarea>
                                </div>

                                <label class="relative inline-flex h-12 w-12 cursor-pointer items-center justify-center rounded-full border border-gray-200 bg-white text-gray-700 transition hover:bg-gray-50">
                                    <input
                                        type="file"
                                        name="image"
                                        accept="image/*"
                                        class="hidden"
                                        x-on:change="imageName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                    >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-9-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                                    </svg>
                                    <span
                                        x-cloak
                                        x-show="imageName"
                                        class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-green-500"
                                    ></span>
                                </label>

                                <button type="submit" class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-900 text-white hover:bg-black">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M13 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                            </div>
                        </form>
                    @else
                        <div class="flex min-h-[520px] items-center justify-center px-6 py-10 text-center">
                            <div>
                                <div class="text-xl font-semibold text-gray-900">Alege o conversatie</div>
                                <div class="mt-2 text-sm text-gray-500">
                                    Intra in dialog cu sellerii, clientii sau adminul direct din platforma.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div
            x-cloak
            x-show="isOpen"
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 px-4 py-6"
            x-on:keydown.escape.window="closeStory()"
        >
            <div class="relative flex h-full max-h-[92vh] w-full max-w-md flex-col overflow-hidden rounded-[2rem] bg-neutral-950 text-white shadow-2xl">
                <div class="absolute inset-x-0 top-0 z-20 px-4 pt-4">
                    <div class="flex gap-1.5">
                        <template x-for="(item, index) in timeline" :key="item.id">
                            <div class="h-1 flex-1 overflow-hidden rounded-full bg-white/20">
                                <div class="h-full rounded-full bg-white transition-all duration-300" :style="progressStyle(index)"></div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-3">
                        <a class="flex items-center gap-3" :href="currentItem() ? currentItem().seller_url : '#'" x-on:click.stop>
                            <div class="h-11 w-11 overflow-hidden rounded-full bg-white/10 ring-2 ring-white/40">
                                <template x-if="isOpen && currentItem() && currentItem().seller_avatar">
                                    <img :src="currentItem().seller_avatar" :alt="currentItem().seller_name" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!isOpen || !currentItem() || !currentItem().seller_avatar">
                                    <div class="flex h-full w-full items-center justify-center text-sm font-semibold" x-text="currentItem() ? currentItem().seller_initial : 'S'"></div>
                                </template>
                            </div>
                            <div>
                                <div class="text-sm font-semibold" x-text="currentItem() ? currentItem().seller_name : ''"></div>
                                <div class="text-xs text-white/70" x-text="currentItem() && currentItem().expires_at ? `Expira ${currentItem().expires_at}` : ''"></div>
                            </div>
                        </a>

                        <button type="button" x-on:click="closeStory()" class="rounded-full bg-white/10 px-3 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-white/80 hover:bg-white/20">
                            Inchide
                        </button>
                    </div>
                </div>

                <div class="relative flex-1 bg-black">
                    <button type="button" x-on:click="prev()" class="absolute inset-y-0 left-0 z-10 w-1/3" aria-label="Story precedent"></button>
                    <button type="button" x-on:click="next()" class="absolute inset-y-0 right-0 z-10 w-1/3" aria-label="Story urmator"></button>

                    <div class="flex h-full items-center justify-center pt-24">
                        <template x-if="isOpen && currentItem() && currentItem().media_type === 'video'">
                            <div class="flex h-full w-full items-center justify-center">
                                <video
                                    x-ref="storyVideo"
                                    :src="currentItem().media_url"
                                    class="h-full w-full object-contain"
                                    playsinline
                                    controls
                                    autoplay
                                    x-on:loadedmetadata="$refs.storyVideo.muted = false; $refs.storyVideo.volume = 1"
                                    x-on:timeupdate="updateVideoProgress()"
                                    x-on:ended="next()"
                                    x-on:error="videoError = true"
                                ></video>
                            </div>
                        </template>

                        <template x-if="isOpen && currentItem() && currentItem().media_type !== 'video'">
                            <img :src="currentItem().media_url" :alt="currentItem() ? currentItem().seller_name : 'Story'" class="h-full w-full object-contain">
                        </template>
                    </div>
                </div>

                <div class="border-t border-white/10 bg-black/70 px-4 py-4 backdrop-blur">
                    <template x-if="currentItem() && currentItem().caption">
                        <div class="mb-3 text-sm text-white/85" x-text="currentItem().caption"></div>
                    </template>

                    <div class="flex items-center gap-3">
                        <div class="min-w-0 flex-1">
                            <input
                                type="text"
                                x-model="storyMessageText"
                                x-on:keydown.enter.prevent="sendStoryMessage()"
                                class="w-full rounded-full border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/45 focus:border-white/20 focus:outline-none focus:ring-0"
                                placeholder="Scrie ceva sellerului despre acest story..."
                            >
                        </div>

                        <button
                            type="button"
                            x-on:click="toggleStoryLike()"
                            class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-white/10 bg-white/10 text-white transition hover:bg-white/20"
                            :class="currentItem() && currentItem().is_liked ? 'text-pink-400' : 'text-white'"
                            :disabled="storyLikeBusy"
                            aria-label="Apreciaza story"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" :fill="currentItem() && currentItem().is_liked ? 'currentColor' : 'none'" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m11.995 21.438-.317-.286C5.51 15.607 1.5 11.978 1.5 7.5a5.25 5.25 0 0 1 9.554-3.071L12 5.746l.946-1.317A5.25 5.25 0 0 1 22.5 7.5c0 4.478-4.01 8.107-10.178 13.652l-.327.286Z" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            x-on:click="sendStoryMessage()"
                            class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-gray-900 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="storyMessageBusy || !storyMessageText.trim()"
                            aria-label="Trimite mesaj"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M13 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const seen = JSON.parse(window.localStorage.getItem('iazos_seen_stories') || '{}');
            document.querySelectorAll('.story-ring[data-story-ids]').forEach((element) => {
                try {
                    const ids = JSON.parse(element.dataset.storyIds || '[]');
                    const allSeen = ids.length > 0 && ids.every((id) => !!seen[String(id)]);
                    element.classList.add(allSeen ? 'bg-gray-300' : 'bg-gradient-to-br', ...(allSeen ? [] : ['from-orange-400', 'via-fuchsia-500', 'to-blue-500']));
                } catch (e) {
                    element.classList.add('bg-gradient-to-br', 'from-orange-400', 'via-fuchsia-500', 'to-blue-500');
                }
            });
        })();
    </script>

    <div
        x-cloak
        x-show="toastMessage"
        x-transition.opacity.duration.180ms
        class="fixed bottom-5 left-1/2 z-[60] -translate-x-1/2 rounded-full bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-xl"
        x-text="toastMessage"
    ></div>
    </div>
</x-app-layout>
