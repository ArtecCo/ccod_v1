<span class="inline-flex items-center gap-1 font-medium {{ $granted ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
    <span aria-hidden="true">{{ $granted ? '✓' : '✕' }}</span>
    {{ $granted ? 'Granted' : 'Not granted' }}
</span>
