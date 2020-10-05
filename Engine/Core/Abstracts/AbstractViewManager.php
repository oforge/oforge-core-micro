<?php
/*****************************************************
 *      OFORGE
 *      Copyright (c) 7P.konzepte GmbH
 *      License: MIT
 *                (                           (
 *               ( ,)                        ( ,)
 *              ). ( )                      ). ( )
 *             (, )' (.                    (, )' (.
 *            \WWWWWWWW/                  \WWWWWWWW/
 *             '--..--'                    '--..--'
 *                }{                          }{
 *                {}                          {}
 *              _._._                       _._._
 *             _|   |_                     _|   |_
 *             | ... |_._._._._._._._._._._| ... |
 *             | ||| |  o   MUCH FORGE  o  | ||| |
 *             | """ |  """    """    """  | """ |
 *        ())  |[-|-]| [-|-]  [-|-]  [-|-] |[-|-]|  ())
 *       (())) |     |---------------------|     | (()))
 *      (())())| """ |  """    """    """  | """ |(())())
 *      (()))()|[-|-]|  :::   .-"-.   :::  |[-|-]|(()))()
 *      ()))(()|     | |~|~|  |_|_|  |~|~| |     |()))(()
 *         ||  |_____|_|_|_|__|_|_|__|_|_|_|_____|  ||
 *      ~ ~^^ @@@@@@@@@@@@@@/=======\@@@@@@@@@@@@@@ ^^~ ~
 *           ^~^~                                ~^~^
 **********************************************************/

namespace Oforge\Engine\Core\Abstracts;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AbstractViewManager
 *
 * @package Oforge\Engine\Core\Abstracts
 */
abstract class AbstractViewManager {

    /**
     * Assign data from a controller to a template
     *
     * @param array $data
     *
     * @return AbstractViewManager
     */
    public abstract function assign(array $data);

    /**
     * Fetch view data. This function should be called from the route middleware
     * so that it can transport the data to the TemplateEngine
     *
     * @return array
     */
    public abstract function fetch();

    /**
     * Get a specific key value from the viewData or $default if data with key does not exist.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public abstract function get(string $key, $default = null);

    /**
     * Check if a specific key exists and is not empty
     *
     * @param string $key
     *
     * @return bool
     */
    public abstract function has(string $key) : bool;

    /**
     * Delete a specific key
     *
     * @return mixed
     */
    public abstract function delete(string $key);

    /**
     * Render response.
     *
     * @param Request $request
     * @param Response $response
     * @param array $data
     *
     * @return Response
     */
    public abstract function render(Request $request, Response $response, array $data) : Response;

}
