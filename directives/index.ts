/* eslint-disable @typescript-eslint/no-explicit-any */
import clickOutside from "./click-outside";
import focus from "./focus";
import tooltip from "./tooltip";
import resize from "./resize";
import scrollLock from "./scroll-lock";

export { clickOutside, focus, tooltip, resize, scrollLock };

// Plugin installation function
export default {
  install(app: any) {
    app.directive("click-outside", clickOutside);
    app.directive("focus", focus);
    app.directive("tooltip", tooltip);
    app.directive("resize", resize);
    app.directive("scroll-lock", scrollLock);
  },
};
