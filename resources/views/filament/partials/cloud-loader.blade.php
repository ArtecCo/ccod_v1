<style>
.ccod-livewire-placeholder{display:flex;min-height:13rem;width:100%;align-items:center;justify-content:center;padding:2rem 1rem;color:var(--primary-600,var(--primary-500,#64748b))}
.ccod-livewire-placeholder__content{display:flex;flex-direction:column;align-items:center;gap:.75rem;color:var(--gray-600,#4b5563);font-size:.875rem;font-weight:500}
.dark .ccod-livewire-placeholder__content{color:var(--gray-300,#d1d5db)}
.ccod-livewire-placeholder__cloud{position:relative;display:grid;width:4.4rem;height:3.5rem;place-items:center;color:var(--primary-600,var(--primary-500,#64748b))}
.ccod-livewire-placeholder__cloud svg{position:absolute;inset:0;width:100%;height:100%;fill:color-mix(in srgb,currentColor 12%,transparent);stroke:currentColor;stroke-width:2.5}
.ccod-livewire-placeholder__cube{position:relative;width:.8rem;height:.8rem;margin-top:.4rem;transform-style:preserve-3d;animation:ccod-tab-cube-spin 1.8s linear infinite}
.ccod-livewire-placeholder__cube span{position:absolute;inset:0;border:1px solid currentColor;background:color-mix(in srgb,currentColor 55%,transparent)}
.ccod-livewire-placeholder__cube span:nth-child(1){transform:translateZ(.4rem)}
.ccod-livewire-placeholder__cube span:nth-child(2){transform:rotateY(180deg) translateZ(.4rem)}
.ccod-livewire-placeholder__cube span:nth-child(3){transform:rotateY(90deg) translateZ(.4rem)}
.ccod-livewire-placeholder__cube span:nth-child(4){transform:rotateY(-90deg) translateZ(.4rem)}
.ccod-livewire-placeholder__cube span:nth-child(5){transform:rotateX(90deg) translateZ(.4rem)}
.ccod-livewire-placeholder__cube span:nth-child(6){transform:rotateX(-90deg) translateZ(.4rem)}
@media(prefers-reduced-motion:reduce){.ccod-livewire-placeholder__cube{animation:none!important}}
</style>
