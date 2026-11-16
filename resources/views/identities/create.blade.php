<x-layouts::app :title="__('Create Digital Identity')">
    <div class="mx-auto max-w-3xl space-y-6">
        <header>
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-emerald-700 hover:underline dark:text-emerald-300">&larr; Back to dashboard</a>
            <h1 class="mt-4 text-3xl font-semibold text-zinc-950 dark:text-white">Create Your Digital Identity</h1>
            <p class="mt-3 text-sm leading-6 text-zinc-600 dark:text-zinc-300">Enter your details below. After submitting, you'll receive a unique Digital Identity ID (DID), and an administrator will review your submission.</p>
        </header>

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-100">
            <strong>Trying a school demonstration?</strong> Use fictional identity information and sample documents, not your real national ID or passport.
        </div>

        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900 dark:border-rose-900 dark:bg-rose-950">
                <p class="font-semibold">Please correct the following information:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('identity.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Your information</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Fields marked * are required.</p>

                <div class="mt-6 space-y-5">
                    <div>
                        <label for="legal_name" class="text-sm font-medium text-zinc-950 dark:text-white">Full name *</label>
                        <input id="legal_name" name="legal_name" type="text" autocomplete="name" value="{{ old('legal_name', auth()->user()->name) }}" required maxlength="255" class="mt-2 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                        @error('legal_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="identity_type" class="text-sm font-medium text-zinc-950 dark:text-white">Type of ID *</label>
                        <select id="identity_type" name="identity_type" required class="mt-2 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                            <option value="">Select an ID type</option>
                            @foreach (['National ID', 'Student ID', 'Passport', 'Driver License'] as $type)
                                <option value="{{ $type }}" @selected(old('identity_type') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('identity_type') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="identity_number" class="text-sm font-medium text-zinc-950 dark:text-white">ID number *</label>
                        <input id="identity_number" name="identity_number" type="text" autocomplete="off" required maxlength="120" class="mt-2 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">We store a protected fingerprint of this number instead of the number itself.</p>
                        @error('identity_number') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="document_file" class="text-sm font-medium text-zinc-950 dark:text-white">Supporting ID document (optional)</label>
                        <p class="mt-1 text-xs leading-5 text-zinc-500 dark:text-zinc-400">PDF, JPG, PNG or WEBP, up to 4 MB. The file is kept private for authorized review.</p>
                        <input id="document_file" name="document_file" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="mt-3 block w-full rounded-lg border border-zinc-300 bg-white p-3 text-sm text-zinc-950 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                        @error('document_file') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">What happens next?</h2>
                <ol class="mt-4 list-inside list-decimal space-y-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                    <li>The system creates your unique DID.</li>
                    <li>Your identity status becomes <strong>Pending verification</strong>.</li>
                    <li>An administrator reviews your submitted information.</li>
                    <li>You can return to the dashboard to check the result.</li>
                </ol>
                <p class="mt-4 text-xs text-zinc-500 dark:text-zinc-400">A cryptocurrency wallet or technical blockchain knowledge is not required to submit an identity.</p>
            </section>
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center rounded-lg border border-zinc-300 px-5 py-3 text-sm font-medium text-zinc-800 dark:border-zinc-700 dark:text-zinc-200">Cancel</a>
                <button type="submit" class="inline-flex min-h-11 items-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Submit for Verification</button>
            </div>
        </form>
    </div>
</x-layouts::app>
