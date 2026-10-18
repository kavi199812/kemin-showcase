<?php

namespace App\Livewire\Admin\Companies;

use App\Livewire\Forms\CompanyFormObject;
use App\Models\Company;
use enshrined\svgSanitize\Sanitizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Edit Company — Admin')]
class CompanyForm extends Component
{
    use WithFileUploads;

    public CompanyFormObject $form;

    public bool $isEditing = false;

    public ?int $companyId = null;

    /**
     * @var UploadedFile|null
     */
    public $newLogo = null;

    public ?string $currentLogoUrl = null;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $company = Company::with(['media'])->findOrFail($id);
            Gate::authorize('update', $company);

            $this->isEditing = true;
            $this->companyId = $id;
            $this->form->setCompany($company);
            $this->currentLogoUrl = $company->getLogoUrl('card');
        } else {
            Gate::authorize('create', Company::class);
            $this->isEditing = false;
            $this->form->country = 'Sri Lanka';
        }
    }

    public function updatedFormName(): void
    {
        $this->form->generateSlug();
    }

    public function deleteLogo(): void
    {
        if ($this->companyId) {
            $company = Company::findOrFail($this->companyId);
            $company->clearMediaCollection('logo');
            $this->currentLogoUrl = null;
            $this->dispatch('notify', message: 'Company logo removed.');
        }
    }

    public function save(): mixed
    {
        $this->validate([
            'newLogo' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,svg', 'max:5120'],
        ]);

        $userId = auth()->id();

        if ($this->isEditing) {
            $company = $this->form->update($userId);
            $message = "Company '{$company->name}' updated successfully.";
        } else {
            $company = $this->form->store($userId);
            $message = "Company '{$company->name}' created successfully.";
        }

        // Process uploaded logo per security rule 6
        if ($this->newLogo) {
            $tempPath = $this->newLogo->getRealPath();
            $mime = $this->newLogo->getMimeType();
            $ext = strtolower($this->newLogo->getClientOriginalExtension());

            if ($ext === 'svg' || str_contains($mime, 'svg')) {
                // SVG sanitization using enshrined/svg-sanitize
                $sanitizer = new Sanitizer;
                $sanitizer->minify(true);
                $cleanContent = $sanitizer->sanitize(file_get_contents($tempPath));

                $cleanFilename = Str::uuid()->toString().'.svg';
                $cleanPath = storage_path('app/temp_'.$cleanFilename);
                file_put_contents($cleanPath, $cleanContent);

                $company->addMedia($cleanPath)
                    ->usingFileName($cleanFilename)
                    ->toMediaCollection('logo');

                @unlink($cleanPath);
            } else {
                // Raster image re-encoding with Intervention to strip EXIF and ensure clean raster
                $manager = new ImageManager(new Driver);
                $img = $manager->read($tempPath);

                $cleanFilename = Str::uuid()->toString().'.webp';
                $cleanPath = storage_path('app/temp_'.$cleanFilename);
                $img->toWebp(90)->save($cleanPath);

                $company->addMedia($cleanPath)
                    ->usingFileName($cleanFilename)
                    ->toMediaCollection('logo');

                @unlink($cleanPath);
            }
        }

        $this->dispatch('notify', message: $message);

        return redirect()->route('admin.companies.index');
    }

    public function render(): View
    {
        return view('livewire.admin.companies.company-form');
    }
}
