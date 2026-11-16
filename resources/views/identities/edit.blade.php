<x-layouts::app :title="__('Correct My Identity')">
    <div class="mx-auto max-w-3xl space-y-6">
        <header>
            <a href="{{ route('identity.index') }}" class="text-sm font-medium text-emerald-700 hover:underline dark:text-emerald-300">&larr; Back to My Identity</a>
            <h1 class="mt-4 text-3xl font-semibold text-zinc-950 dark:text-white">Correct Your Identity Details</h1>
            <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">Make the needed changes, then send your identity back to the administrator for another review.</p>
        </header>

        <div class="rounded-xl border border-rose-200 bg-rose-50 p-5 text-rose-900 dark:border-rose-900 dark:bg-rose-950 dark:text-rose-100">
            <h2 class="font-semibold">Why your identity was rejected</h2>
            <p class="mt-2 text-sm leading-6">{{ $identity->rejection_reason ?: 'The administrator did not provide a reason. Contact support before resubmitting.' }}</p>
        </div>

        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900 dark:border-rose-900 dark:bg-rose-950 dark:text-rose-100">
                <strong>Please check the form:</strong>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('identity.resubmit', $identity) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')
            <section class="space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <div>
                    <label for="legal_name" class="text-sm font-medium text-zinc-950 dark:text-white">Full name *</label>
                    <input id="legal_name" name="legal_name" type="text" required maxlength="255" value="{{ old('legal_name', $identity->legal_name) }}" class="mt-2 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                    @error('legal_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="identity_type" class="text-sm font-medium text-zinc-950 dark:text-white">Type of ID *</label>
                    <select id="identity_type" name="identity_type" required class="mt-2 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                        @foreach (['National ID', 'Student ID', 'Passport', 'Driver License'] as $type)
                            <option value="{{ $type }}" @selected(old('identity_type', $identity->identity_type) === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('identity_type') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="identity_number" class="text-sm font-medium text-zinc-950 dark:text-white">New ID number (only if it needs correcting)</label>
                    <input id="identity_number" name="identity_number" type="text" maxlength="120" autocomplete="off" class="mt-2 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">We cannot display the previous ID number because it was stored as a protected fingerprint. Leave this empty to keep it unchanged.</p>
                    @error('identity_number') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="document_file" class="text-sm font-medium text-zinc-950 dark:text-white">Replace supporting document (optional)</label>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">PDF, JPG, PNG or WEBP, up to 4 MB. Leave empty to keep the existing document.</p>
                    @if ($identity->document_path)<p class="mt-2 text-xs text-zinc-600 dark:text-zinc-300">Current file: {{ $identity->document_original_name ?: 'Uploaded document' }}</p>@endif
                    <input id="document_file" name="document_file" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="mt-3 block w-full rounded-lg border border-zinc-300 bg-white p-3 text-sm text-zinc-950 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                    @error('document_file') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </section>

            <div class="flex flex-wrap justify-end gap-3">
                <a href="{{ route('identity.index') }}" class="inline-flex min-h-11 items-center rounded-lg border border-zinc-300 px-5 py-3 text-sm font-medium text-zinc-800 dark:border-zinc-700 dark:text-zinc-200">Cancel</a>
                <button type="submit" class="inline-flex min-h-11 items-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Save Changes and Resubmit</button>
            </div>
        </form>
    </div>
</x-layouts::app>
