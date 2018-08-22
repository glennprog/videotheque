<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaginatorManager
{
    protected $requestStack;
    protected $router;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router){
        $this->setRequestStack($requestStack);
        $this->router = $router;
    }

    public function getPaginatorPageCount(){
        $request = $this->getRequestStack()->getCurrentRequest();

        if($request->query->get('page') != null){
            $page = $request->query->get('page');
        }
        elseif($request->attributes->get('page') != null){
            $page = $request->attributes->get('page');
        }
        else{
            $page = 1;
        }

        if($request->query->get('count') != null){
            $count = $request->query->get('count');
        }
        elseif($request->attributes->get('count') != null){
            $count = $request->attributes->get('count');
        }
        else{
            $count = 5;
        }
        return array('page' => $page, 'count' => $count);
    }

    public function getPaginatorAttributes(array $paginatorPageCount = null, $total = null, array $route = null, $entityNameHandled = null){
        if($paginatorPageCount == null){
            $paginatorPageCount = $this->getPaginatorPageCount();
        }
        $page = $paginatorPageCount['page'];
        $count = $paginatorPageCount['count'];
        
        $init_read = false;
        if($page < 1 || $count < 1){
            $init_read = true;
        }
        $count = ($init_read) ? 1 : $count;
        $page = ($init_read) ? 1 : $page; // $count($page - 1)
        $paginator['count'] = $count;
        $paginator['total_page'] = intval(ceil($total / $count));
        $paginator['current_page'] = ($init_read) ? 1 : $page;
        $paginator['previous_page'] = ($paginator['current_page'] > 1) && ($paginator['current_page'] <= $paginator['total_page']) ? ($paginator['current_page'] - 1) : null;
        $paginator['next_page'] = ($total - ($page * $count) > 0) ? ($page + 1) : null;
        $paginator['next_record_to_read'] = ($paginator['next_page'] != null) ? ($total - ($count * $page)) : null;
        $init_read = false;
        $paginator['total_entities'] = $total;

        if($route['params'] != null){
            foreach ($route['params'] as $key => $value) {
                $routeParams[$key] = $value;
            }
        }

        $routeParams['page'] = $paginator['previous_page'];
        $routeParams['count'] = $paginator['count'];
        $paginator["paginator_prev"] = ($paginator['previous_page'] == null) ? null : $this->router->generate(
            $route['route_name'], 
            $routeParams
        );

        $routeParams['page'] = 1;
        $routeParams['count'] = $paginator['count'];
        $paginator["paginator_prev_fast"] = $this->router->generate(
            $route['route_name'], 
            $routeParams
        );
 
        $routeParams['page'] = $paginator['next_page'];
        $routeParams['count'] = $paginator['count'];
        $paginator["paginator_next"] = ($paginator['next_page'] == null) ? null : $this->router->generate(
            $route['route_name'], 
            $routeParams
        );

        $routeParams['page'] = $paginator['total_page'];
        $routeParams['count'] = $paginator['count'];
        $paginator["paginator_next_fast"] = $this->router->generate(
            $route['route_name'], 
            $routeParams
        );
        $paginator["entity"] = $entityNameHandled;
        //$paginator["url"]
        return $paginator;
    }

    protected function getRequestStack(){
		return $this->requestStack;
	}

	protected function setRequestStack($requestStack){
		$this->requestStack = $requestStack;
    }
}
