"""
Capa de acceso HTTP a la API central FastAPI.
Un único punto de contacto: BASE_URL, headers y errores centralizados.
"""

import os
import requests as http


class ApiException(Exception):
    """Se lanza cuando FastAPI responde con status >= 400."""

    def __init__(self, status_code: int, detail: str):
        self.status_code = status_code
        self.detail = str(detail)
        super().__init__(self.detail)


class ApiClient:
    BASE_URL = os.getenv("API_URL", "http://localhost:8001")

    @staticmethod
    def _raise_for_status(resp: http.Response) -> dict:
        if resp.status_code >= 400:
            try:
                detail = resp.json().get("detail", resp.text)
            except Exception:
                detail = resp.text
            raise ApiException(resp.status_code, detail)
        return resp.json()

    @staticmethod
    def get(path: str, params: dict = None, auth: tuple = None) -> dict:
        """GET request. Si auth=True, usa credenciales macuin/123456."""
        kwargs = {"params": params}
        if auth:
            kwargs["auth"] = auth
        resp = http.get(f"{ApiClient.BASE_URL}{path}", **kwargs)
        return ApiClient._raise_for_status(resp)

    @staticmethod
    def post(path: str, json: dict = None, data: dict = None, files=None, params: dict = None) -> dict:
        resp = http.post(
            f"{ApiClient.BASE_URL}{path}",
            json=json,
            data=data,
            files=files,
            params=params,
        )
        return ApiClient._raise_for_status(resp)

    @staticmethod
    def put(path: str, json: dict = None, data: dict = None, files=None) -> dict:
        resp = http.put(
            f"{ApiClient.BASE_URL}{path}",
            json=json,
            data=data,
            files=files,
        )
        return ApiClient._raise_for_status(resp)

    @staticmethod
    def patch(path: str, json: dict = None) -> dict:
        resp = http.patch(f"{ApiClient.BASE_URL}{path}", json=json)
        return ApiClient._raise_for_status(resp)

    @staticmethod
    def delete(path: str, auth: tuple = ("macuin", "123456")) -> dict:
        resp = http.delete(f"{ApiClient.BASE_URL}{path}", auth=auth)
        return ApiClient._raise_for_status(resp)

    @staticmethod
    def get_raw(path: str, auth: tuple = None, params: dict = None) -> http.Response:
        """Descarga de archivos — devuelve Response sin procesar (para reportes)."""
        kwargs = {}
        if auth:
            kwargs["auth"] = auth
        if params:
            kwargs["params"] = params
        return http.get(f"{ApiClient.BASE_URL}{path}", **kwargs)
