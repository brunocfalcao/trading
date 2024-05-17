<?php

namespace Brunocfalcao\Trading\Abstracts;

use Illuminate\Support\ServiceProvider;

class TradingServiceProvider extends ServiceProvider
{
    protected $dir;

    public function boot()
    {
        $this->loadMigrationsFrom($this->dir.'/../database/migrations');

        $this->registerPolicies();
        $this->registerObservers();
        $this->registerGlobalScopes();
    }

    public function register()
    {
        //
    }

    protected function registerPolicies()
    {
        $modelPaths = glob($this->dir.'/Models/*.php');
        $modelClasses = array_map(function ($path) {
            return basename($path, '.php');
        }, $modelPaths);

        foreach ($modelClasses as $model) {
            $modelClass = "\\Brunocfalcao\\Trading\\Models\\{$model}";
            $policyClass = "\\Brunocfalcao\\Trading\\Policies\\{$model}Policy";

            try {
                if (class_exists($modelClass) && class_exists($policyClass)) {
                    $modelClassObject = new $modelClass;
                    $policyClassObject = new $policyClass;

                    Gate::policy(get_class($modelClassObject), get_class($policyClassObject));
                }
            } catch (\Exception $ex) {
                info('Policy Registration Error: '.$ex->getMessage());
            }
        }
    }

    protected function registerGlobalScopes()
    {
        $modelPaths = glob($this->dir.'/Models/*.php');
        $modelClasses = array_map(function ($path) {
            return basename($path, '.php');
        }, $modelPaths);

        foreach ($modelClasses as $model) {
            $modelClass = "\\Brunocfalcao\\Trading\\Models\\{$model}";
            $scopeClass = "\\Brunocfalcao\\Trading\\Scopes\\{$model}Scope";

            try {
                if (class_exists($modelClass) && class_exists($scopeClass)) {
                    $modelClass::addGlobalScope(new $scopeClass);
                }
            } catch (\Exception $ex) {
            }
        }
    }

    protected function registerObservers()
    {
        $modelPaths = glob($this->dir.'/Models/*.php');
        $modelClasses = array_map(function ($path) {
            return basename($path, '.php');
        }, $modelPaths);

        foreach ($modelClasses as $model) {
            $modelClass = "\\Brunocfalcao\\Trading\\Models\\{$model}";
            $observerClass = "\\Brunocfalcao\\Trading\\Observers\\{$model}Observer";

            try {
                if (class_exists($modelClass) && class_exists($observerClass)) {
                    $modelClass::observe($observerClass);
                }
            } catch (\Exception $ex) {
                info('Observer Registration Error: '.$ex->getMessage());
            }
        }
    }
}
