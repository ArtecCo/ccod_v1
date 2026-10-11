<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('styling.brand_name', 'CCOD');
        $this->migrator->add('styling.logo_light', null);
        $this->migrator->add('styling.logo_dark', null);
        $this->migrator->add('styling.favicon', null);
        $this->migrator->add('styling.font_family', 'Inter');
        $this->migrator->add('styling.primary_color', 'amber');
        $this->migrator->add('styling.success_color', 'success');
        $this->migrator->add('styling.warning_color', 'warning');
        $this->migrator->add('styling.danger_color', 'danger');
        $this->migrator->add('styling.info_color', 'info');
        $this->migrator->add('styling.gray_color', 'gray');
        $this->migrator->add('styling.corner_radius', '0.375rem');
    }
};
