// composables/useHeaderBackground.ts

type RouteBackgrounds = Record<string, string>;

/**
 * Composable for managing header background based on routes
 */
export const useHeaderBackground = () => {
  // Create or access shared state
  const backgroundsState = useState<RouteBackgrounds>(
    "header-backgrounds",
    () => ({
      "/": "bg-gradient-to-r from-blue-600 to-indigo-800",
      "/consultancy":
        "bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800",
      "/courses": "bg-gradient-to-r from-indigo-600 to-purple-600",
      // Add more routes as needed
    })
  );

  /**
   * Get background for a specific route, with fallback to default
   */
  const getBackgroundForRoute = (route: string): string => {
    // Try to find exact match first
    if (backgroundsState.value[route]) {
      return backgroundsState.value[route];
    }

    // If not found, try to find a parent route match (for nested routes)
    for (const path in backgroundsState.value) {
      // Check if route starts with any of the defined paths
      if (route.startsWith(path) && path !== "/") {
        return backgroundsState.value[path];
      }
    }

    // Default fallback
    return backgroundsState.value["/"] || "bg-transparent";
  };

  /**
   * Add or update a background for a specific route
   */
  const setRouteBackground = (route: string, background: string): void => {
    backgroundsState.value = { ...backgroundsState.value, [route]: background };
  };

  return {
    backgrounds: backgroundsState,
    getBackgroundForRoute,
    setRouteBackground,
  };
};
