export const getApiBase = () => {
  return import.meta.env.BASE_URL.replace(/(\/)build(\/)?/, "")
}
