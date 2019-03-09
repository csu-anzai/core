///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="workingIndicator"/>

import * as $ from "jquery";

class WorkingIndicator {
  private static intWorkingCount = 0;

  public static start() {
    if (this.intWorkingCount == 0) {
      $("#status-indicator").addClass("active");
    }
    this.intWorkingCount++;
  }

  public static stop() {
    this.intWorkingCount--;

    if (this.intWorkingCount == 0) {
      $("#status-indicator").removeClass("active");
    }
  }

  public static getInstance() {
    return WorkingIndicator;
  }
}

export default WorkingIndicator;
