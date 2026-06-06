<x-guest-layout>
    <x-slot name="title">Registration Pending</x-slot>

    <div class="text-center py-4">

        {{-- Checkmark icon --}}
        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-50 border-2 border-primary-200 rounded-full mb-5">
            <x-ui.icon name="check-circle" class="w-8 h-8 text-primary-600" />
        </div>

        <div class="w-8 h-0.5 bg-primary-500 mx-auto mb-4"></div>
        <h2 class="text-2xl font-bold text-gray-900">Registration Submitted</h2>
        <p class="text-sm text-gray-500 mt-2 leading-relaxed max-w-xs mx-auto">
            Your account is now pending review. A municipal health officer will approve your account before you can log in.
        </p>
    </div>

    <div class="mt-6 border border-amber-200 bg-amber-50 px-4 py-4 space-y-2">
        <p class="text-sm font-semibold text-amber-800 flex items-center gap-2">
            <x-ui.icon name="exclamation-triangle" class="w-4 h-4 shrink-0" />
            What happens next?
        </p>
        <ul class="text-sm text-amber-700 space-y-1 pl-6 list-disc">
            <li>The admin will review your registration details.</li>
            <li>Once approved, you can log in using your credentials.</li>
            <li>If you have questions, visit the Luna, Apayao Municipal Health Office.</li>
        </ul>
    </div>

    <div class="mt-6">
        <a href="{{ route('login') }}"
           class="block w-full text-center px-4 py-2.5 bg-primary-700 text-white text-sm font-semibold hover:bg-primary-800 transition-colors">
            Back to Login
        </a>
    </div>

</x-guest-layout>
