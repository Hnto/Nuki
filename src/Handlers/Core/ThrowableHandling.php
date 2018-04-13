<?php
namespace Nuki\Handlers\Core;

class ThrowableHandling {

  /**
   * Initialize and set handler method as exception handler
   */
  public function init() {
    set_exception_handler([$this, 'handler']);
  }

    /**
     * Handle throwable
     *
     * @param \Throwable $t
     *
     * @return mixed
     *
     * @throws \Nuki\Exceptions\Base
     */
    public function handler(\Throwable $t) {
        //Write to log
        $this->toLog($t);

        if (!in_array(strtolower(Assist::getAppEnv()), ['dev', 'development', 'local'])) {
          //Write to log
          $this->toLog($t);

          //Write a user friendly message to output
          $msg = Assist::loadCoreView('whoops');

          return $this->toUserOutput($msg);
        }

        return $this->toOutput($t);
    }

  /**
   * Write to output
   *
   * @param \Throwable $t
   *
   * @return mixed
   */
  private function toOutput(\Throwable $t) {
    //Write to output
    $renderer = new \Nuki\Handlers\Http\Output\Renderers\RawRenderer();
    $response = new \Nuki\Handlers\Http\Output\Response($renderer);

    $content = get_class($t) . ': ';
    $content .= $t->getMessage();
    $content .= ' in ';
    $content .= $t->getFile() . ':' . $t->getLine();

    $renderer->setContent(new \Nuki\Models\IO\Output\Content($content));

    $response->httpStatusCode(\Nuki\Models\IO\Output\Http::HTTP_EXPECTATION_FAILED);

    $response->send();

    return;
  }

  /**
   * Write message to user output
   *
   * @param string $msg
   * @return mixed
   */
  private function toUserOutput(string $msg) {
    $renderer = new \Nuki\Handlers\Http\Output\Renderers\RawRenderer();
    $response = new \Nuki\Handlers\Http\Output\Response($renderer);

    $renderer->setContent(new \Nuki\Models\IO\Output\Content($msg));

    $response->httpStatusCode(\Nuki\Models\IO\Output\Http::HTTP_EXPECTATION_FAILED);

    $response->send();

    return;
  }

    /**
     * @param \Throwable $t
     *
     * @return mixed
     */
  private function toLog(\Throwable $t)
  {
    return;
  }
}

