export const BaseUrlVerifier = (baseUrl: string): void => {
  if (baseUrl !== window.location.origin) {
    window.location.href = window.location.href.replace(
      window.location.origin,
      baseUrl,
    );
  }
};
