<style>
/* Center the lazy component placeholder within the visible tab content. */
.ccod-livewire-placeholder {
    box-sizing: border-box;
    display: grid;
    width: 100%;
    min-height: max(24rem, 55vh);
    place-items: center;
    padding: 2.5rem 1rem;
}
.ccod-livewire-placeholder__content {
    display: flex;
    align-items: center;
    flex-direction: column;
    gap: .55rem;
    text-align: center;
}
.ccod-livewire-placeholder__cloud {
    display: block;
    width: 4.5rem;
    height: 3.4rem;
    margin-bottom: .55rem;
    overflow: visible;
    fill: var(--primary-600, var(--primary-500, #64748b));
    transform-origin: 50% 55%;
    animation: ccod-cloud-pulse 1.65s ease-in-out infinite;
}
.ccod-livewire-placeholder__label {
    color: var(--gray-700, #374151);
    font-size: .9rem;
    font-weight: 600;
    letter-spacing: .01em;
}
.dark .ccod-livewire-placeholder__label {
    color: var(--gray-200, #e5e7eb);
}
.ccod-livewire-placeholder__hint {
    color: var(--gray-500, #6b7280);
    font-size: .78rem;
}
.dark .ccod-livewire-placeholder__hint {
    color: var(--gray-400, #9ca3af);
}
@keyframes ccod-cloud-pulse {
    0%, 100% { opacity: .72; transform: scale(.94); }
    50% { opacity: 1; transform: scale(1.06); }
}
@media (max-height: 600px) {
    .ccod-livewire-placeholder { min-height: 22rem; }
}
@media (prefers-reduced-motion: reduce) {
    .ccod-livewire-placeholder__cloud { animation: none; }
}
</style>
