<?php

namespace WilcarJose\Wapi\Http;

use League\Fractal\Manager;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\Resource\Collection;
//use League\Fractal\TransformerAbstract;
use WilcarJose\Wapi\Transformers\DataArraySerializer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

trait WapiResponse
{
    /**
     * Status code of response
     *
     * @var int
     */
    protected $statusCode = 200;
 
    /**
     * Fractal manager instance
     *
     * @var Manager
     */
    protected $fractal;

    /**
     * Event of error message
     *
     * @var string
     */
    protected $event;

    /**
     * Array of errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Array of debug trace
     *
     * @var array
     */
    protected $debug = [];
 
    /**
     * Set fractal Manager instance
     *
     * @param Manager $fractal
     *
     * @return void
     */
    public function setFractal(Manager $fractal)
    {
        $this->fractal = $fractal;
    }
 
    /**
     * Getter for statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return (int) $this->statusCode;
    }
 
    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function statusCode($statusCode)
    {
        $this->statusCode = $statusCode;
 
        return $this;
    }

    /**
     * Setter for event for error message
     *
     * @param string $event
     *
     * @return self
     */
    public function event($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Setter for errors messages
     *
     * @param array $errors
     *
     * @return self
     */
    public function errors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Setter for debug trace
     *
     * @param array $debug
     *
     * @return self
     */
    public function debug($debug)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Setter status code 400
     *
     * @return $this
     */
    public function status400()
    {
        $this->statusCode = 400;
 
        return $this;
    }

    /**
     * Setter status code 403
     *
     * @return $this
     */
    public function status403()
    {
        $this->statusCode = 403;
 
        return $this;
    }

    /**
     * Setter status code 405
     *
     * @return $this
     */
    public function status405()
    {
        $this->statusCode = 405;
 
        return $this;
    }
 
    /**
     * Send custom data response
     *
     * @param $status
     * @param $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCustomResponse($status, $message)
    {
        return response()->json([
            'status'  => $status,
            'message' => $message
        ], $status);
    }

    /**
     * Sends the json message response
     *
     * @param $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function send($message = '')
    {
        $event = $this->event ? $this->event : 'error';

        $message = !empty($message) ? $message : trans("errors.$event");
        
        $this->statusCode ? $this->statusCode : 400;

        $data = [
            'status'  => $this->statusCode,
            'message' => $message
        ];

        if (!empty($this->errors)) {
            $data['errors'] = $this->errors;
        }

        if (config('app.debug') && !empty($this->debug)) {
            $data['debug'] = $this->debug;
        }

        return response()->json($data, $this->statusCode);
    }

    /**
     * Send ok response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOk()
    {
        return $this->sendCustomResponse(200, 'Ok');
    }

    /**
     * Send No Content
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNoContent()
    {
        return response()->json([], 204);
    }
 
    /**
     * Send this response when api user provide fields that doesn't exist in our application
     *
     * @param $errors
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendUnknownField($errors)
    {
        return response()->json([
            'status'  => 400,
            'message' => trans('errors.unknown_fields'),
            'errors'  => $errors,
        ], 400);
    }
 
    /**
     * Send this response when api user provide filter that doesn't exist in our application
     *
     * @param $errors
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInvalidFilter($errors)
    {
        return response()->json([
            'status'  => 400,
            'message' => trans('errors.invalid_filters'),
            'errors'  => $errors
        ], 400);
    }
 
    /**
     * Send this response when api user provide incorrect data type for the field
     *
     * @param $errors
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInvalidField($errors)
    {
        return response()->json([
            'status'  => 422,
            'message' => trans('errors.invalid_fields'),
            'errors'  => $errors
        ], 422);
    }
 
    /**
     * Send this response when a api user try access a resource that they don't belong
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendForbiddenResponse()
    {
        return response()->json([
            'status'  => 403,
            'message' => trans('errors.forbidden'),
        ], 403);
    }

    /**
     * Send this response when a api user try access a resource that they don't belong
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInvalidCredentials()
    {
        return response()->json([
            'status'  => 401,
            'message' => trans('errors.invalid_credentials'),
        ], 401);
    }
 
    /**
     * Send 404 not found response
     *
     * @param string $event
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotFound($event = '')
    {
        $event = !empty($event) ? $event : 'not_found';

        return response()->json(['status' => 404, 'message' => trans("errors.$event")], 404);
    }

    /**
     * Send 404 not found message
     *
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotFoundMessage($message)
    {
        return response()->json(['status' => 404, 'message' => $message ], 404);
    }

    /**
     * Send 404 specific object not found response
     *
     * @param string $object
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendObjectNotFound($object)
    {
        $this->message = trans("wapi::errors.resource_not_found.$object");

        return $this->sendNotFoundMessage($this->message);
    }

    /**
     * Send could not update resource
     *
     * @param string $event
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCouldNotUpdate($event = '')
    {
        $event = !empty($event) ? $event : 'update_error';

        $data = [
            'status'  => 400,
            'message' => trans("errors.$error")
        ];

        if (!empty($this->errors)) {
            $data['errors'] = $this->errors;
        }

        if (config('app.debug') && !empty($this->debug)) {
            $data['debug'] = $this->debug;
        }

        return response()->json($data, $data['status']);
    }
 
    /**
     * Send empty data response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmptyData()
    {
        return response()->json(['data' => new \StdClass()]);
    }
 
    /**
     * Return collection response from the application
     *
     * @param array|LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection $collection
     * @param \Closure|TransformerAbstract $callback
     * @param array $params Used for filters, sort, includes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithCollection($collection, $callback, $include = '', $fields = '')
    {
        $this->fractal->setSerializer(new DataArraySerializer());

        $this->fractal->parseIncludes($include);

        $callback->filterFields($fields);
        
        $resource = new Collection($collection, $callback, 'data');
 
        //set empty data pagination
        if (empty($collection)) {
            $collection = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $resource = new Collection($collection, $callback);
        }

        $resource->setPaginator(new IlluminatePaginatorAdapter($collection));

        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray(), false);
    }
 
    /**
     * Return single item response from the application
     *
     * @param Model $item
     * @param \Closure|TransformerAbstract $callback
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithItem($item, $callback)
    {
        $resource = new Item($item, $callback);

        $rootScope = $this->fractal->createData($resource);
 
        return $this->respondWithArray($rootScope->toArray(), false);
    }
 
    /**
     * Return a json response from the application
     *
     * @param array   $data
     * @param boolean $dataItem
     * @param array   $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithArray(array $data, $dataItem = true, array $headers = [])
    {
        $data = $dataItem ? compact('data') : $data;

        return response()->json($data, $this->statusCode, $headers);
    }

    /**
     * Return json item or not found object
     *
     * @param  mixed  $object
     * @param  mixed  $transformer
     * @param  string $resource
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function item($object, $transformer, $resource)
    {
        return $object
            ? $this->respondWithItem($object, $transformer)
            : $this->sendObjectNotFound($resource);
    }

    /**
     * Return json collection or empty data
     *
     * @param  array        $collection
     * @param  mixed        $transformer
     * @param  ParamService $params
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection($collection, $transformer, $params)
    {
        return $collection->isNotEmpty()
            ? $this->respondWithCollection(
                $collection,
                $transformer,
                $params->getInclude(),
                $params->getFields()
            )
            : $this->sendEmptyData();
    }

    /**
     * Determines if request is an api call.
     *
     * If the request URI contains '/api/v'.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function isApiCall(Request $request)
    {
        return $request->is('api/*');
    }
}
