<?php
namespace App\Services\Mobile;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;

/** Calls only controller classes selected by a server-side registry. */
class WebAction
{
    public function run(Request $request, string $controller, string $method, ?Model $model = null, array $extra = [])
    {
        $reflection = new \ReflectionMethod($controller, $method);
        $route = new Route(['POST'], 'mobile-action', ['uses'=>$controller.'@'.$method]);
        $parameters = $extra;
        foreach ($reflection->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type instanceof \ReflectionNamedType && is_a($type->getName(), Model::class, true)) {
                $modelClass = $type->getName();
                abort_unless($model instanceof $modelClass, 404);
                $parameters[$parameter->getName()] = $model;
            }
        }
        $route->bind($request);
        foreach ($parameters as $name=>$value) {
            $route->setParameter($name, $value);
            $route->setParameter(\Illuminate\Support\Str::snake($name), $value);
        }
        $copy = Request::createFrom($request);
        $copy->setRouteResolver(fn()=>$route);
        $session=new \Illuminate\Session\Store('mobile_action',new \Illuminate\Session\ArraySessionHandler(120));
        $session->start();$copy->setLaravelSession($session);
        $previousRedirector=app('redirect');
        $redirector=new \Illuminate\Routing\Redirector(app('url'));
        $redirector->setSession($session);
        $arguments = [];
        foreach ($reflection->getParameters() as $parameter) {
            $type = $parameter->getType()?->getName();
            if ($type && is_a($type, FormRequest::class, true)) {
                $form = $type::createFrom($copy, new $type);
                $form->setContainer(app())->setRedirector($redirector);
                $form->validateResolved();
                $arguments[] = $form;
            } elseif ($type && is_a($type, Request::class, true)) {
                $arguments[] = $copy;
            } elseif (array_key_exists($parameter->getName(), $parameters)) {
                $arguments[] = $parameters[$parameter->getName()];
            } elseif ($type && !$parameter->getType()->isBuiltin()) {
                $arguments[] = app($type);
            } else {
                throw new \LogicException('Unsupported mobile controller argument.');
            }
        }
        app()->instance('redirect',$redirector);
        try {
            $result = $reflection->invokeArgs(app($controller), $arguments);
            $errors = $session->get('errors');
            if ($errors && $errors->any()) throw ValidationException::withMessages($errors->getBag('default')->messages());
            if ($session->has('error')) throw ValidationException::withMessages(['action'=>$session->get('error')]);
            return $result;
        } finally { app()->instance('redirect',$previousRedirector); }
    }
}
