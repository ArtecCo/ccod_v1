<style>
/* Center the lazy component placeholder within the visible tab content. */
.ccod-livewire-placeholder {
    box-sizing: border-box;
    display: grid;
    width: 100%;
    min-height: max(24rem, 55vh);
    place-items: center;
    padding: 2.5rem 1rem;
    color: var(--primary-600, var(--primary-500, #64748b));
}
.ccod-livewire-placeholder__content {
    display: flex;
    align-items: center;
    flex-direction: column;
    gap: .45rem;
    text-align: center;
}
.ccod-livewire-placeholder__cloud {
    position: relative;
    display: grid;
    width: 5rem;
    height: 3.75rem;
    margin-bottom: .75rem;
    place-items: center;
    color: var(--primary-600, var(--primary-500, #64748b));
}
.ccod-livewire-placeholder__cloud svg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    overflow: visible;
    fill: color-mix(in srgb, currentColor 8%, transparent);
    stroke: currentColor;
    stroke-width: 2.25;
}
.ccod-livewire-placeholder__cube {
    position: relative;
    z-index: 1;
    width: 1.05rem;
    height: 1.05rem;
    margin-top: .55rem;
    border: 1.5px solid currentColor;
    border-radius: .12rem;
    background: color-mix(in srgb, currentColor 24%, transparent);
    box-shadow: .32rem .32rem 0 -1px color-mix(in srgb, currentColor 45%, transparent);
    transform: rotate(45deg);
    animation: ccod-metrics-cube 1.8s ease-in-out infinite;
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
@keyframes ccod-metrics-cube {
    0%, 100% { transform: translateY(0) rotate(45deg); }
    50% { transform: translateY(-.3rem) rotate(225deg); }
}
@media (max-height: 600px) {
    .ccod-livewire-placeholder { min-height: 22rem; }
}
@media (prefers-reduced-motion: reduce) {
    .ccod-livewire-placeholder__cube { animation: none; }
}
</style>
